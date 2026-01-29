<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('items');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('name')->paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    // public function destroy(Category $category)
    // {
    //     if ($category->items()->exists()) {
    //         return redirect()->route('categories.index')
    //             ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki barang.');
    //     }

    //     $category->delete();

    //     return redirect()->route('categories.index')
    //         ->with('success', 'Kategori berhasil dihapus.');
    // }

    public function destroy(\App\Models\Category $category)
    {
        // 1. Cek Barang Aktif (User wajib pindahkan manual barang yang masih dipakai)
        if ($category->items()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih menampung barang aktif. Silakan pindahkan barangnya terlebih dahulu.');
        }

        // --- REVISI LOGIC (Agar tidak bentrok dengan Database) ---
        // Karena kolom category_id TIDAK BOLEH NULL, maka barang yang sudah
        // ada di tempat sampah (soft deleted) kita musnahkan permanen (Force Delete)
        // supaya kategori ini bisa bebas dihapus.

        \App\Models\Item::onlyTrashed()
            ->where('category_id', $category->id)
            ->forceDelete();

        // ---------------------------------------

        // 2. Hapus Kategori
        try {
            $category->delete();
            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan database: ' . $e->getMessage());
        }
    }
}
