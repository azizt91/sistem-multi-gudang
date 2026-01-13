<?php

namespace App\Exports;

use App\Models\WarehouseItem;
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
    protected $warehouseId;

    public function __construct($categoryId = null, $lowStockOnly = false, $warehouseId = null)
    {
        $this->categoryId = $categoryId;
        $this->lowStockOnly = $lowStockOnly;
        $this->warehouseId = $warehouseId;
    }

    public function collection()
    {
        $query = WarehouseItem::with(['item.category', 'item.unit', 'warehouse']);

        if ($this->categoryId) {
            $query->whereHas('item', function ($q) {
                $q->where('category_id', $this->categoryId);
            });
        }

        if ($this->warehouseId) {
            $query->where('warehouse_id', $this->warehouseId);
        }

        if ($this->lowStockOnly) {
            $query->whereColumn('stock', '<=', 'minimum_stock');
        }

        return $query->get()->sortBy(function ($warehouseItem) {
            // Sort by Warehouse Name then Item Name
            return $warehouseItem->warehouse->name . $warehouseItem->item->name;
        });
    }

    public function headings(): array
    {
        return [
            'Kota',
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

    public function map($warehouseItem): array
    {
        $isLow = $warehouseItem->stock <= $warehouseItem->minimum_stock;

        return [
            $warehouseItem->warehouse->city ?? '-',
            $warehouseItem->item->code,
            $warehouseItem->item->name,
            $warehouseItem->item->category->name,
            $warehouseItem->item->unit->abbreviation,
            $warehouseItem->stock,
            $warehouseItem->minimum_stock,
            $isLow ? 'STOK MENIPIS' : 'Normal',
            $warehouseItem->item->rack_location ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
