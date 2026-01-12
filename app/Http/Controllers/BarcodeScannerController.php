<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class BarcodeScannerController extends Controller
{
    public function index()
    {
        if (auth()->user()->isOwner()) {
            abort(403);
        }

        // Scope warehouses
        $warehouses = auth()->user()->isStaff()
            ? \App\Models\Warehouse::where('id', auth()->user()->warehouse_id)->get()
            : \App\Models\Warehouse::orderBy('name')->get();

        return view('barcode.scanner', compact('warehouses'));
    }

    public function submitTransaction(Request $request)
    {
        if (auth()->user()->isOwner()) abort(403);

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out',
            'notes' => 'nullable|string',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        // Security Check
        if (auth()->user()->isStaff() && $validated['warehouse_id'] != auth()->user()->warehouse_id) {
             return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
    }

    public function search(Request $request)
    {
        $code = $request->input('code');
        $item = Item::with(['category', 'unit'])->where('code', $code)->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Barang dengan kode "' . $code . '" tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'category' => $item->category->name,
                'unit' => $item->unit->abbreviation,
                'unit_name' => $item->unit->name,
                'stock' => $item->stock,
                'minimum_stock' => $item->minimum_stock,
                'rack_location' => $item->rack_location,
                'is_low_stock' => $item->isLowStock(),
                'barcode' => $item->generateBarcode(),
            ],
        ]);
    }
}
