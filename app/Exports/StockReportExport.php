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
        // 1. Get Warehouses (All or Filtered)
        $warehousesQuery = \App\Models\Warehouse::orderBy('name');
        if($this->warehouseId) {
            $warehousesQuery->where('id', $this->warehouseId);
        }
        $warehouses = $warehousesQuery->get();

        // 2. Get Items (All or Filtered)
        $itemsQuery = \App\Models\Item::with(['category', 'unit', 'warehouseItems'])->orderBy('name');
        if ($this->categoryId) {
            $itemsQuery->where('category_id', $this->categoryId);
        }
        $allItems = $itemsQuery->get();

        // 3. Build Matrix
        $reportData = collect();

        foreach ($warehouses as $warehouse) {
            foreach ($allItems as $item) {
                // Determine stock for this specific pair
                $warehouseItem = $item->warehouseItems->firstWhere('warehouse_id', $warehouse->id);
                $stock = $warehouseItem->stock ?? 0;
                // Fix for 0 minimum stock override issue
                $localMinStock = $warehouseItem->minimum_stock ?? 0;
                $minStock = ($localMinStock > 0) ? $localMinStock : $item->minimum_stock;
                
                // Filter Low Stock if requested
                if ($this->lowStockOnly && $stock > $minStock) {
                    continue;
                }

                $reportData->push((object)[
                    'warehouse_name' => $warehouse->name,
                    'city' => $warehouse->city ?? '-',
                    'item_code' => $item->code,
                    'item_name' => $item->name,
                    'category_name' => $item->category->name,
                    'unit_name' => $item->unit->abbreviation,
                    'stock' => (int) ($warehouseItem->stock ?? 0),
                    'minimum_stock' => (int) ($minStock),
                    'rack_location' => $item->rack_location,
                    'status' => $stock <= $minStock ? 'Low Stock' : 'Normal',
                ]);
            }
        }

        return $reportData;
    }

    public function headings(): array
    {
        return [
            'Gudang',
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

    public function map($row): array
    {
        // Since we constructed objects in collection(), map is simpler or even redundant if structure matches.
        // But let's keep it for explicit ordering/formatting.
        return [
            $row->warehouse_name,
            $row->city,
            $row->item_code,
            $row->item_name,
            $row->category_name,
            $row->unit_name,
            (string) $row->stock, // Force string to ensure "0" displays in Excel
            (string) $row->minimum_stock,
            $row->status === 'Low Stock' ? 'STOK MENIPIS' : 'Normal',
            $row->rack_location ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
