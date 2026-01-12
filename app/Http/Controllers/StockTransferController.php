<?php

namespace App\Http\Controllers;


use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Models\Item;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $query = StockTransfer::with(['sourceWarehouse', 'destinationWarehouse', 'user'])
            ->latest();

        if ($request->filled('warehouse_id')) {
            $query->where(function($q) use ($request) {
                $q->where('source_warehouse_id', $request->warehouse_id)
                  ->orWhere('destination_warehouse_id', $request->warehouse_id);
            });
        }

        $transfers = $query->paginate(15);
        $warehouses = Warehouse::orderBy('name')->get();

        return view('stock-transfers.index', compact('transfers', 'warehouses'));
    }

    public function create()
    {
        // Only Admin/Staff can transfer (Owner view only)
        if (auth()->user()->isOwner()) abort(403);

        $warehouses = Warehouse::orderBy('name')->get();
        // Get all items, logic for availability handles in validation/UI
        $items = Item::with('unit')->orderBy('name')->get();

        return view('stock-transfers.create', compact('warehouses', 'items'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->isOwner()) abort(403);

        $rules = [
            'source_warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'required|exists:warehouses,id|different:source_warehouse_id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',
        ];

        // Security: Staff must transfer FROM their assigned warehouse
        if (auth()->user()->isStaff()) {
            if ($request->source_warehouse_id != auth()->user()->warehouse_id) {
                // If they tried to spoof it, fail validation or abort
                 abort(403, 'Anda hanya dapat melakukan transfer DARI gudang Anda sendiri.');
            }
        }

        $validated = $request->validate($rules);

        try {
            DB::transaction(function () use ($validated) {
                // 1. Create Stock Transfer Record
                $transfer = StockTransfer::create([
                    'source_warehouse_id' => $validated['source_warehouse_id'],
                    'destination_warehouse_id' => $validated['destination_warehouse_id'],
                    'notes' => $validated['notes'],
                    'user_id' => auth()->id(),
                    'status' => 'completed', // Created = Completed for now
                ]);

                // START AUDIT LOG
                $this->auditService->log(
                    'transfer_stock',
                    "Melakukan transfer stok ({$transfer->transfer_number}) dari {$transfer->sourceWarehouse->name} ke {$transfer->destinationWarehouse->name}",
                    $transfer
                );
                // END AUDIT LOG

                // 2. Create Stock Out (Source)
                $outHeader = $this->stockService->createTransaction(
                    'out',
                    $validated['items'],
                    "Transfer Keluar ke " . $transfer->destinationWarehouse->name . " (Ref: {$transfer->transfer_number})",
                    $validated['source_warehouse_id']
                );

                // 3. Create Stock In (Destination)
                $inHeader = $this->stockService->createTransaction(
                    'in',
                    $validated['items'],
                    "Transfer Masuk dari " . $transfer->sourceWarehouse->name . " (Ref: {$transfer->transfer_number})",
                    $validated['destination_warehouse_id']
                );

                // 4. Link & Lock Documents
                $outHeader->update([
                    'stock_transfer_id' => $transfer->id,
                    'receipt_locked' => true
                ]);
                
                $inHeader->update([
                    'stock_transfer_id' => $transfer->id,
                    'receipt_locked' => true
                ]);

            });

            return redirect()->route('stock-transfers.index')
                ->with('success', 'Transfer stok berhasil dicatat.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses transfer: ' . $e->getMessage())->withInput();
        }
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['sourceWarehouse', 'destinationWarehouse', 'user', 'stockHeaders.transactions.item.unit']);
        return view('stock-transfers.show', compact('stockTransfer'));
    }
}
