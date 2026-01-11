<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::withCount('items');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('abbreviation', 'like', '%' . $request->search . '%');
        }

        $units = $query->orderBy('name')->paginate(10);

        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:units,name',
            'abbreviation' => 'required|string|max:10',
        ]);

        Unit::create($validated);

        return redirect()->route('units.index')
            ->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:units,name,' . $unit->id,
            'abbreviation' => 'required|string|max:10',
        ]);

        $unit->update($validated);

        return redirect()->route('units.index')
            ->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->items()->exists()) {
            return redirect()->route('units.index')
                ->with('error', 'Satuan tidak dapat dihapus karena masih digunakan oleh barang.');
        }

        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Satuan berhasil dihapus.');
    }
}
