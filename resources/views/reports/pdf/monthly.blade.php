<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan - {{ $startDate->format('F Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .summary-value {
            font-size: 20px;
            font-weight: bold;
        }
        .summary-label {
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #4F46E5;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-success { color: #10B981; }
        .text-danger { color: #EF4444; }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .badge-success { background: #10B981; color: white; }
        .badge-danger { background: #EF4444; color: white; }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TRANSAKSI BULANAN</h1>
        <p>Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-value text-success">{{ number_format($summary['total_in']) }}</div>
            <div class="summary-label">Total Stok Masuk</div>
        </div>
        <div class="summary-item">
            <div class="summary-value text-danger">{{ number_format($summary['total_out']) }}</div>
            <div class="summary-label">Total Stok Keluar</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ number_format($summary['transaction_count']) }}</div>
            <div class="summary-label">Total Transaksi</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th class="text-center">Jenis</th>
                <th class="text-right">Qty</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $transaction->transaction_date->format('d/m/Y H:i') }}</td>
                <td>{{ $transaction->item->code }}</td>
                <td>{{ Str::limit($transaction->item->name, 25) }}</td>
                <td class="text-center">
                    <span class="badge {{ $transaction->type === 'in' ? 'badge-success' : 'badge-danger' }}">
                        {{ $transaction->type === 'in' ? 'MASUK' : 'KELUAR' }}
                    </span>
                </td>
                <td class="text-right {{ $transaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                    {{ $transaction->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                </td>
                <td>{{ $transaction->user->name }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada transaksi pada bulan ini</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | Warehouse Management System
    </div>
</body>
</html>
