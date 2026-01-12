<?php

namespace App\Exports;

use App\Models\StockTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $type;
    protected $warehouseId;

    public function __construct($startDate = null, $endDate = null, $type = null, $warehouseId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->type = $type;
        $this->warehouseId = $warehouseId;
    }

    public function collection()
    {
        $query = StockTransaction::with(['item', 'user', 'stockHeader.warehouse']);

        if ($this->startDate && $this->endDate) {
            $query->dateRange($this->startDate, $this->endDate);
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->warehouseId) {
            $query->whereHas('stockHeader', fn($q) => $q->where('warehouse_id', $this->warehouseId));
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal & Waktu',
            'Gudang',
            'Kode Barang',
            'Nama Barang',
            'Jenis Transaksi',
            'Stok Awal',
            'Masuk',
            'Keluar',
            'Stok Akhir',
            'User',
            'Catatan',
        ];
    }

    public function map($transaction): array
    {
        $masuk = $transaction->type === 'in' ? $transaction->quantity : 0;
        $keluar = $transaction->type === 'out' ? $transaction->quantity : 0;

        return [
            $transaction->transaction_date->format('d/m/Y H:i'),
            $transaction->stockHeader->warehouse->name ?? '-',
            $transaction->item->code,
            $transaction->item->name,
            $transaction->type === 'in' ? 'Stok Masuk' : 'Stok Keluar',
            (string) $transaction->stock_before,
            (string) $masuk,
            (string) $keluar,
            (string) $transaction->stock_after,
            $transaction->user->name,
            $transaction->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
