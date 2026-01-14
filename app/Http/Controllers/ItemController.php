<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'unit']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by warehouse (if needed to show items present in specific warehouse)
        $warehouseId = $request->warehouse_id;

        // Force Staff to their own warehouse
        if (auth()->user()->isStaff()) {
            $warehouseId = auth()->user()->warehouse_id;
        }

        if ($warehouseId) {
             // For filtering (optional: only show items present in warehouse? or show all but load specific stock?)
             // User wants: Show ALL items (Master), but Stock column specific to warehouse.
             // So we DON'T filter the main query by warehouseItems existence (or maybe we do if they only want to see what's physically there?)
             // "Rekomendasi teknisnya: Untuk Daftar Nama Barang (Master Data): Tetap tampilkan SEMUA barang... Tapi untuk Kolom Sisa Stok: Wajib menampilkan angka HANYA dari gudang dia sendiri."
             // So we do NOT use whereHas here to filter rows. We ONLY use eager load for calculation.
             
             // BUT wait, if admin selects a warehouse filter, usually they expect to see items IN that warehouse.
             // Let's keep existing logic: if warehouse_id is passed, we check existence?
             // Actually, if we want to show Master Data but with local stock, we shouldn't filter master data rows.
             // However, standard expectation of a "Filter" is to narrow items.
             // Let's stick to: Show ALL items, but load correct stock relation.
             
             // UPDATE: Previous code used whereHas, implying strict filtering.
             // User agreed to my recommendation: "Tetap tampilkan SEMUA barang... Tapi untuk Kolom Sisa Stok: HANYA dari gudang dia".
             // So I should REMOVE `whereHas` and only keep eager loading.
             // UNLESS the user explicitly wants to "Filter by Warehouse" to see what is inside.
             // For Staff, usually they view the index as Master List.
             
             // Let's support both:
             // 1. Eager load specific warehouse items (CRITICAL for stock calc)
             $query->with(['warehouseItems' => function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
             }]);
             
             // 2. If 'filter_strict' or similar is passed? No.
             // Let's assume standard behavior for now is Just Contextual Stock.
             // But valid "Filter" UI usually implies narrowing results.
             // Let's comment out whereHas for now to follow "Master Data" principle, matches "list 15 item" logic.
             /* 
             $query->whereHas('warehouseItems', function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
             });
             */
        }

        // Filter by low stock
        if ($request->boolean('low_stock')) {
            $query->lowStock();
        }

        $items = $query->orderBy('name')->paginate(15);
        $categories = Category::orderBy('name')->get();
        // Warehouses for filter dropdown (Admin only needs full list, Staff needs self or handled in View)
        $warehouses = auth()->user()->isStaff() 
            ? \App\Models\Warehouse::where('id', auth()->user()->warehouse_id)->get() 
            : \App\Models\Warehouse::orderBy('name')->get();

        if ($request->ajax()) {
            return view('items.partials.table', compact('items'))->render();
        }

        return view('items.index', compact('items', 'categories', 'warehouses'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $suggestedCode = Item::generateCode();

        return view('items.create', compact('categories', 'units', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:items,code',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            // Stock is handled via transactions, not initial setup
            'minimum_stock' => 'required|integer|min:0',
            'rack_location' => 'nullable|string|max:50',
        ]);

        // Force stock to 0 explicitly if needed, though DB default should handle it
        // If Item model has 'stock' in fillable but not in $validated, create() will ignore it unless merged.
        // We trust DB default or Model default.
        Item::create($validated);

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        $item->load(['category', 'unit', 'stockTransactions' => function ($query) {
            $query->with(['user', 'stockHeader'])
                  ->when(auth()->user()->isStaff(), function($q) {
                        // Filter transactions where the header is for their warehouse
                        $q->whereHas('stockHeader', function($h) {
                           $h->where('warehouse_id', auth()->user()->warehouse_id);
                        });
                  })
                  ->orderBy('transaction_date', 'desc')
                  ->limit(20);
        }]);

        // For Staff, eager load only their warehouse item to ensure 'stock' accessor uses local stock
        if (auth()->user()->isStaff()) {
            $item->load(['warehouseItems' => function($q) {
                $q->where('warehouse_id', auth()->user()->warehouse_id);
            }]);
        } else {
             // For Admin, maybe we want to show all warehouse items breakdown
             $item->load('warehouseItems.warehouse');
        }

        return view('items.show', compact('item'));
    }

    public function edit(Request $request, Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        
        $warehouseId = $request->query('warehouse_id');
        $contextWarehouse = null;

        if ($warehouseId) {
            $contextWarehouse = \App\Models\Warehouse::findOrFail($warehouseId);
            // Load specific warehouse item stock settings
            $wi = $item->warehouseItems()->where('warehouse_id', $warehouseId)->first();
            
            // Override item properties for the view
            if ($wi) {
                // If local min stock is set (>0), use it. If 0, it currently falls back to global in logic, 
                // but for EDITING, we want to show what is effective or allow setting it.
                // If it is 0, we show 0? Or show global?
                // User wants to SEE current setting.
                // If our logic is "0 means inherit", we should ideally show the inherited value but maybe indicate it?
                // For simplicity: Show the value from WarehouseItem if it exists.
                // If user sees 0, and wants 3, they type 3.
                // However, previous fix assumed "If 0, use Global". 
                // So if we show 0, user might think it's 0. 
                // Let's pre-fill with the EFFECTIVE value, but if they save, it becomes explicit.
                
                $effectiveMin = ($wi->minimum_stock > 0) ? $wi->minimum_stock : $item->minimum_stock;
                $item->minimum_stock = $effectiveMin;
            } else {
                // No record yet, so it effectively uses global
                // We keep $item->minimum_stock as is (Global)
            }
        }

        return view('items.edit', compact('item', 'categories', 'units', 'contextWarehouse'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:items,code,' . $item->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'minimum_stock' => 'required|integer|min:0',
            'rack_location' => 'nullable|string|max:50',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        // Don't allow direct stock update - must go through transactions
        unset($validated['stock']);
        
        // Handle Warehouse Context Update
        if (!empty($validated['warehouse_id'])) {
            $warehouseId = $validated['warehouse_id'];
            
            // Update or Create Warehouse Item Configuration
            // We only update minimum_stock here contextually
            $wi = \App\Models\WarehouseItem::updateOrCreate(
                ['warehouse_id' => $warehouseId, 'item_id' => $item->id],
                ['minimum_stock' => $validated['minimum_stock']]
            );
            
            // Remove minimum_stock from global update to preserve master setting
            unset($validated['minimum_stock']);
            
            // Optional: Prevent other global fields from updating if we want strict separation?
            // But usually fixing a name typo should apply globally even if in context.
            // So we proceed with remaining validated data.
        }

        $item->update($validated);

        // Redirect back to index with same filter if possible, or just index
        $redirect = redirect()->route('items.index');
        if (!empty($validated['warehouse_id'])) {
             $redirect->with('warehouse_id', $validated['warehouse_id']);
        }
        
        return $redirect->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        // Soft delete
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    /**
     * Show barcode for printing
     */
    public function barcode(Item $item)
    {
        return view('items.barcode', compact('item'));
    }

    /**
     * Print multiple barcodes as PDF
     */
    public function printBarcodes(Request $request)
    {
        // Check if printing all items based on current filter
        if ($request->boolean('print_all')) {
            $query = Item::with(['category', 'unit']);

            // Apply same filters as index
            if ($request->filled('filter_search')) {
                $query->search($request->filter_search);
            }
            if ($request->filled('filter_category_id')) {
                $query->where('category_id', $request->filter_category_id);
            }
            if ($request->boolean('filter_low_stock')) {
                $query->lowStock();
            }

            $items = $query->orderBy('name')->get();
        } else {
            // Print selected items only
            $validated = $request->validate([
                'item_ids' => 'required|array|min:1',
                'item_ids.*' => 'exists:items,id',
            ]);

            $items = Item::with(['category', 'unit'])
                ->whereIn('id', $validated['item_ids'])
                ->orderBy('name')
                ->get();
        }

        if ($items->isEmpty()) {
            return back()->with('error', 'Tidak ada barang untuk dicetak.');
        }

        $quantity = $request->input('quantity', 1);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('items.pdf.barcodes', compact('items', 'quantity'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('barcodes-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Find item by code (for barcode scanning)
     */
    public function findByCode(Request $request)
    {
        $code = $request->input('code');
        $item = Item::with(['category', 'unit'])->findByCode($code);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Barang dengan kode "' . $code . '" tidak ditemukan.',
            ], 404);
        }

        // Check if warehouse context is provided
        $warehouseId = $request->input('warehouse_id');
        $stock = $warehouseId ? $item->getStockInWarehouse($warehouseId) : $item->stock;

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'category' => $item->category->name,
                'unit' => $item->unit->abbreviation,
                'stock' => $stock,
                'rack_location' => $item->rack_location,
                'is_low_stock' => $item->isLowStock(),
                'warehouse_id' => $warehouseId, // Echo back
            ],
        ]);
    }
    /**
     * List items as JSON (for dropdowns)
     */
    public function listItems(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        $search = $request->input('search');

        $query = Item::with('unit');

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Warehouse filter (if strictly filtering items present in warehouse)
        if ($warehouseId) {
            // Eager load only relevant warehouse items for stock calc
             $query->with(['warehouseItems' => function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
             }]);
        }

        $items = $query->orderBy('name')->get()->map(function($item) use ($warehouseId) {
            // Calculate stock based on context
            $stock = $warehouseId ? $item->getStockInWarehouse($warehouseId) : $item->stock;
            
            return [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'unit' => $item->unit->abbreviation,
                'stock' => $stock
            ];
        });

        return response()->json($items);
    }
}
