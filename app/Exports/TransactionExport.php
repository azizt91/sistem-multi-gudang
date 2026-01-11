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

    public function __construct($startDate = null, $endDate = null, $type = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->type = $type;
    }

    public function collection()
    {
        $query = StockTransaction::with(['item', 'user']);

        if ($this->startDate && $this->endDate) {
            $query->dateRange($this->startDate, $this->endDate);
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kode Barang',
            'Nama Barang',
            'Jenis Transaksi',
            'Jumlah',
            'Stok Sebelum',
            'Stok Sesudah',
            'User',
            'Catatan',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_date->format('d/m/Y H:i'),
            $transaction->item->code,
            $transaction->item->name,
            $transaction->type === 'in' ? 'Stok Masuk' : 'Stok Keluar',
            $transaction->quantity,
            $transaction->stock_before,
            $transaction->stock_after,
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
