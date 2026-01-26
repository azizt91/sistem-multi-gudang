<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockHeader;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockService
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Create a stock transaction with multiple items (document-based)
     */
    public function createTransaction(string $type, array $items, ?string $notes = null, ?int $warehouseId = null): StockHeader
    {
        return DB::transaction(function () use ($type, $items, $notes, $warehouseId) {
            // Validate warehouse
            if (!$warehouseId) {
                $warehouseId = Warehouse::first()->id;
            }

            // Create the header/document
            $header = StockHeader::create([
                'warehouse_id' => $warehouseId,
                'type' => $type,
                'transaction_date' => now(),
                'notes' => $notes,
                'user_id' => Auth::id(),
            ]);

            // START AUDIT LOG
            $typeLabel = $type === StockHeader::TYPE_IN ? 'Masuk' : 'Keluar';
            $this->auditService->log(
                'create_transaction',
                "Membuat transaksi stok {$typeLabel} ({$header->document_number}) di gudang {$header->warehouse->name}",
                $header
            );
            // END AUDIT LOG

            // Process each item
            foreach ($items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);
                $quantity = (int) $itemData['quantity'];
                $itemNotes = $itemData['notes'] ?? null;

                // Determine stock from WarehouseItem
                $warehouseItem = WarehouseItem::firstOrCreate(
                    ['warehouse_id' => $warehouseId, 'item_id' => $item->id],
                    ['stock' => 0, 'minimum_stock' => $item->minimum_stock] // Inherit global min stock
                );

                $stockBefore = $warehouseItem->stock;

                if ($type === StockHeader::TYPE_IN) {
                    $stockAfter = $stockBefore + $quantity;
                } else {
                    if ($quantity > $stockBefore) {
                        throw new \Exception("Stok {$item->name} di gudang ini tidak mencukupi. Stok saat ini: {$stockBefore}");
                    }
                    $stockAfter = $stockBefore - $quantity;
                }

                // Update warehouse item stock
                $warehouseItem->update(['stock' => $stockAfter]);

                // Create transaction detail
                StockTransaction::create([
                    'stock_header_id' => $header->id,
                    'item_id' => $item->id,
                    'user_id' => Auth::id(),
                    'type' => $type,
                    'quantity' => $quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'notes' => $itemNotes,
                    'transaction_date' => now(),
                ]);
            }

            return $header->fresh(['transactions.item.unit', 'user', 'warehouse']);
        });
    }

    /**
     * Revert a transaction (for deletion)
     */
    public function revertTransaction(StockTransaction $transaction): void
    {
        $warehouseId = $transaction->stockHeader->warehouse_id;
        $warehouseItem = WarehouseItem::where('warehouse_id', $warehouseId)
            ->where('item_id', $transaction->item_id)
            ->first();

        if ($warehouseItem) {
            if ($transaction->type === StockTransaction::TYPE_IN) {
                $warehouseItem->decrement('stock', $transaction->quantity);
            } else {
                $warehouseItem->increment('stock', $transaction->quantity);
            }

            // START AUDIT LOG (Only log once per header revert to avoid spam, or log per line?
            // Revert is usually called in a loop for deletion. Let's log per line for detail or maybe just rely on the controller's "delete_transaction" log.
            // Actually, the controller logs "delete_transaction" which covers the whole event.
            // Logging every line item revert might be too noisy.
            // User requested: "Delete stock transaction" is a tracked action.
            // So logging in Controller is sufficient for the high level action.
            // BUT, if we want detailed traceability of STOCK changes, logging here is better.
            // Let's stick to the high level "Delete Transaction" in the controller as requested to keep logs clean.
            // Wait, I will add it here just in case this method is used elsewhere, but for now Controller-level is the requirement.
            // "Tracked Actions: Delete stock transaction".
            // So I will SKIP adding it here and ensure StockHeaderController logs "delete_transaction".
        }
    }

    /**
     * Helper to apply warehouse scope
     */
    private function applyWarehouseScope($query, $warehouseId = null)
    {
        $user = Auth::user();

        // 1. Staff: Always Force their Warehouse
        if ($user && $user->isStaff()) {
            // Ensure they have a warehouse assigned, fallback to none (0 results) if null
            $query->where('warehouse_id', $user->warehouse_id ?? -1);
            return;
        }

        // 2. Admin/Owner: Filter only if requested
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(?int $warehouseId = null): array
    {
        // Scope warehouse ID for display name
        $finalWarehouseId = $warehouseId;
        if (Auth::user()->isStaff()) {
            $finalWarehouseId = Auth::user()->warehouse_id;
        }

        // Items Count (with stock > 0)
        $itemsQuery = WarehouseItem::query();
        $this->applyWarehouseScope($itemsQuery, $warehouseId);
        $totalItems = $itemsQuery->where('stock', '>', 0)->count();

        // Total Stock
        $stockQuery = WarehouseItem::query();
        $this->applyWarehouseScope($stockQuery, $warehouseId);
        $totalStock = $stockQuery->sum('stock');

        // 3. Low Stock
        // Logic: stock <= effective_min_stock
        // effective_min_stock = (wi.minimum_stock > 0) ? wi.minimum_stock : item.minimum_stock
        // SQL: WHERE stock <= IF(warehouse_items.minimum_stock > 0, warehouse_items.minimum_stock, items.minimum_stock)

        $lowStockQuery = WarehouseItem::query()->join('items', 'warehouse_items.item_id', '=', 'items.id');
        $this->applyWarehouseScope($lowStockQuery, $warehouseId);

        $lowStockCount = $lowStockQuery->whereRaw('warehouse_items.stock <= (CASE WHEN warehouse_items.minimum_stock > 0 THEN warehouse_items.minimum_stock ELSE items.minimum_stock END)')
                                       ->count();

        // 4. Transaction Stats (Today & Month)
        // Helper to scope transactions via header
        $scopeTx = function($query) use ($finalWarehouseId, $warehouseId) {
             // If manual ID provided (Admin filter), use it
             if ($warehouseId) {
                 $query->whereHas('stockHeader', fn($q) => $q->where('warehouse_id', $warehouseId));
                 return;
             }
             // If Staff, use their ID
             if (Auth::user()->isStaff()) {
                 $query->whereHas('stockHeader', fn($q) => $q->where('warehouse_id', Auth::user()->warehouse_id));
             }
             // If Admin/Owner and no ID, no filter (global)
        };

        $todayIn = StockTransaction::today()->stockIn();
        $scopeTx($todayIn);

        $todayOut = StockTransaction::today()->stockOut();
        $scopeTx($todayOut);

        $monthIn = StockTransaction::thisMonth()->stockIn();
        $scopeTx($monthIn);

        $monthOut = StockTransaction::thisMonth()->stockOut();
        $scopeTx($monthOut);

        return [
            'total_items' => $totalItems,
            'total_stock' => (int) $totalStock,
            'low_stock_count' => $lowStockCount,
            'total_stock_in_today' => (int) $todayIn->sum('quantity'),
            'total_stock_out_today' => (int) $todayOut->sum('quantity'),
            'total_stock_in_month' => (int) $monthIn->sum('quantity'),
            'total_stock_out_month' => (int) $monthOut->sum('quantity'),
            // Gunakan optional() atau pengecekan manual
            'warehouse_name' => $finalWarehouseId 
                ? (Warehouse::find($finalWarehouseId)?->name ?? 'Gudang Tidak Dikenal') 
                : 'Semua Gudang',
        ];
    }

    /**
     * Get low stock items
     */
    public function getLowStockItems(int $limit = 5, ?int $warehouseId = null)
    {
        $query = WarehouseItem::with(['item.category', 'item.unit', 'warehouse'])
            ->join('items', 'warehouse_items.item_id', '=', 'items.id')
            ->select('warehouse_items.*');

        $this->applyWarehouseScope($query, $warehouseId);

        return $query->whereRaw('warehouse_items.stock <= (CASE WHEN warehouse_items.minimum_stock > 0 THEN warehouse_items.minimum_stock ELSE items.minimum_stock END)')
                     ->orderBy('warehouse_items.stock', 'asc')
                     ->limit($limit)
                     ->get()
                     ->map(function ($wi) {
                         // --- PERBAIKAN DIMULAI DARI SINI ---

                         $item = $wi->item;

                         // 1. Cek apakah item ada. Jika null (terhapus), kita skip.
                         if (!$item) {
                             return null;
                         }

                         // 2. Assign stock (sekarang aman karena $item sudah dipastikan ada)
                         $item->stock = $wi->stock;

                         // 3. Logika Minimum Stock
                         $item->minimum_stock = ($wi->minimum_stock > 0) ? $wi->minimum_stock : $item->minimum_stock;

                         // 4. Cek Warehouse juga (jaga-jaga kalau gudangnya dihapus)
                         $item->warehouse_name = $wi->warehouse ? $wi->warehouse->name : 'Unknown Warehouse';

                         return $item;
                     })
                     // 5. Filter (buang) data yang null tadi supaya tidak error di View
                     ->filter()
                     ->values();
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(int $limit = 5, ?int $warehouseId = null)
    {
        $query = StockTransaction::with(['item', 'user', 'stockHeader.warehouse'])
            ->orderBy('transaction_date', 'desc');

        // Scope scoping via Header
        $user = Auth::user();
        if ($user && $user->isStaff()) {
             $query->whereHas('stockHeader', fn($q) => $q->where('warehouse_id', $user->warehouse_id));
        } elseif ($warehouseId) {
             $query->whereHas('stockHeader', fn($q) => $q->where('warehouse_id', $warehouseId));
        }

        return $query->limit($limit)->get();
    }
}
