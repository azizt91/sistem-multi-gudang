<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::withCount(['warehouseItems', 'stockHeaders'])->paginate(10);
        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'nullable|string',
            'pic' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil ditambahkan');
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'nullable|string',
            'pic' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil diperbarui');
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->stockHeaders()->exists()) {
            return back()->with('error', 'Gudang tidak bisa dihapus karena memiliki transaksi');
        }

        if ($warehouse->warehouseItems()->where('stock', '>', 0)->exists()) {
            return back()->with('error', 'Gudang tidak bisa dihapus karena masih memiliki stok barang');
        }

        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil dihapus');
    }
}
