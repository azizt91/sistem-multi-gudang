<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class BarcodeScannerController extends Controller
{
    public function index()
    {
        return view('barcode.scanner');
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
