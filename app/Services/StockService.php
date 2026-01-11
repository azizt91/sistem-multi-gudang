<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockHeader;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockService
{
    /**
     * Create a stock transaction with multiple items (document-based)
     * 
     * @param string $type 'in' or 'out'
     * @param array $items Array of ['item_id' => x, 'quantity' => y, 'notes' => z]
     * @param string|null $notes Header-level notes
     * @return StockHeader
     */
    public function createTransaction(string $type, array $items, ?string $notes = null): StockHeader
    {
        return DB::transaction(function () use ($type, $items, $notes) {
            // Create the header/document
            $header = StockHeader::create([
                'type' => $type,
                'transaction_date' => now(),
                'notes' => $notes,
                'user_id' => Auth::id(),
            ]);

            // Process each item
            foreach ($items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);
                $quantity = (int) $itemData['quantity'];
                $itemNotes = $itemData['notes'] ?? null;

                $stockBefore = $item->stock;

                if ($type === StockHeader::TYPE_IN) {
                    $stockAfter = $stockBefore + $quantity;
                } else {
                    if ($quantity > $stockBefore) {
                        throw new \Exception("Stok {$item->name} tidak mencukupi. Stok saat ini: {$stockBefore}");
                    }
                    $stockAfter = $stockBefore - $quantity;
                }

                // Update item stock
                $item->update(['stock' => $stockAfter]);

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

            return $header->fresh(['transactions.item.unit', 'user']);
        });
    }

    /**
     * Process stock in (single item - legacy support)
     */
    public function stockIn(Item $item, int $quantity, ?string $notes = null): StockTransaction
    {
        return DB::transaction(function () use ($item, $quantity, $notes) {
            $stockBefore = $item->stock;
            $stockAfter = $stockBefore + $quantity;

            // Update item stock
            $item->update(['stock' => $stockAfter]);

            // Create transaction record
            return StockTransaction::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => StockTransaction::TYPE_IN,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => $notes,
                'transaction_date' => now(),
            ]);
        });
    }

    /**
     * Process stock out (single item - legacy support)
     */
    public function stockOut(Item $item, int $quantity, ?string $notes = null): StockTransaction
    {
        if ($quantity > $item->stock) {
            throw new \Exception("Stok tidak mencukupi. Stok saat ini: {$item->stock}");
        }

        return DB::transaction(function () use ($item, $quantity, $notes) {
            $stockBefore = $item->stock;
            $stockAfter = $stockBefore - $quantity;

            // Update item stock
            $item->update(['stock' => $stockAfter]);

            // Create transaction record
            return StockTransaction::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => StockTransaction::TYPE_OUT,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => $notes,
                'transaction_date' => now(),
            ]);
        });
    }

    /**
     * Revert a transaction (for deletion)
     */
    public function revertTransaction(StockTransaction $transaction): void
    {
        $item = $transaction->item;
        
        if ($transaction->type === StockTransaction::TYPE_IN) {
            $item->update(['stock' => $item->stock - $transaction->quantity]);
        } else {
            $item->update(['stock' => $item->stock + $transaction->quantity]);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return [
            'total_items' => Item::count(),
            'total_stock_in_today' => StockTransaction::today()->stockIn()->sum('quantity'),
            'total_stock_out_today' => StockTransaction::today()->stockOut()->sum('quantity'),
            'low_stock_count' => Item::lowStock()->count(),
            'total_stock_in_month' => StockTransaction::thisMonth()->stockIn()->sum('quantity'),
            'total_stock_out_month' => StockTransaction::thisMonth()->stockOut()->sum('quantity'),
        ];
    }

    /**
     * Get low stock items
     */
    public function getLowStockItems()
    {
        return Item::with(['category', 'unit'])
            ->lowStock()
            ->orderBy('stock')
            ->get();
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(int $limit = 10)
    {
        return StockTransaction::with(['item', 'user', 'stockHeader'])
            ->orderBy('transaction_date', 'desc')
            ->limit($limit)
            ->get();
    }
}
