<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $categoryId;
    protected $lowStockOnly;

    public function __construct($categoryId = null, $lowStockOnly = false)
    {
        $this->categoryId = $categoryId;
        $this->lowStockOnly = $lowStockOnly;
    }

    public function collection()
    {
        $query = Item::with(['category', 'unit']);

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        if ($this->lowStockOnly) {
            $query->lowStock();
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Stok',
            'Minimum Stok',
            'Status',
            'Lokasi Rak',
        ];
    }

    public function map($item): array
    {
        return [
            $item->code,
            $item->name,
            $item->category->name,
            $item->unit->abbreviation,
            $item->stock,
            $item->minimum_stock,
            $item->isLowStock() ? 'STOK MENIPIS' : 'Normal',
            $item->rack_location ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
