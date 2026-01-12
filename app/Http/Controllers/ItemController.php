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
        if ($request->filled('warehouse_id')) {
             $query->whereHas('warehouseItems', function($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
             });
        }

        // Filter by low stock
        if ($request->boolean('low_stock')) {
            $query->lowStock();
        }

        $items = $query->orderBy('name')->paginate(15);
        $categories = Category::orderBy('name')->get();
        $warehouses = \App\Models\Warehouse::orderBy('name')->get();

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
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'rack_location' => 'nullable|string|max:50',
        ]);

        Item::create($validated);

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        $item->load(['category', 'unit', 'stockTransactions' => function ($query) {
            $query->with('user')->orderBy('transaction_date', 'desc')->limit(20);
        }]);

        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('items.edit', compact('item', 'categories', 'units'));
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
        ]);

        // Don't allow direct stock update - must go through transactions
        unset($validated['stock']);

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil diperbarui.');
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
}
