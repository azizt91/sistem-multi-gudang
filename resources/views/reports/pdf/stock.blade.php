<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok - {{ now()->format('d F Y') }}</title>
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
            display: inline-block;
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
        @php
            $profile = \App\Models\CompanyProfile::first();
        @endphp
        @if($profile && $profile->logo_path)
            <img src="{{ public_path('storage/' . $profile->logo_path) }}" style="height: 40px; margin-bottom: 5px;">
        @endif
        <h1 style="font-size: 16px; margin-top: 5px;">{{ $profile->company_name ?? 'WMS' }}</h1>
        <div style="margin-bottom: 10px; font-size: 10px; color: #666;">{{ $profile->address ?? '' }}</div>

        <h2 style="font-size: 14px; margin-top: 15px; border-top: 1px solid #333; padding-top: 10px;">LAPORAN STOK BARANG</h2>
        <p>Tanggal Cetak: {{ now()->format('d F Y H:i') }}</p>
        <p>
            Gudang: {{ $warehouse ? $warehouse->name : 'Semua Gudang' }} |
            Kategori: {{ $category ? $category->name : 'Semua Kategori' }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Kota</th>
                <th width="15%">Kode Barang</th>
                <th>Nama Barang</th>
                <th width="15%">Kategori</th>
                <th class="text-center" width="8%">Satuan</th>
                <th class="text-center" width="10%">Stok</th>
                <th class="text-center" width="10%">Min. Stok</th>
                <th class="text-center" width="10%">Status</th>
                <th width="10%">Lokasi Rak</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $warehouseItem)
            <tr>
                <td>{{ $warehouseItem->warehouse->city ?? '-' }}</td>
                <td>{{ $warehouseItem->item->code }}</td>
                <td>{{ $warehouseItem->item->name }}</td>
                <td>{{ $warehouseItem->item->category->name }}</td>
                <td class="text-center">{{ $warehouseItem->item->unit->abbreviation }}</td>
                <td class="text-center fw-bold">
                    {{ $warehouseItem->stock }}
                </td>
                <td class="text-center">{{ $warehouseItem->minimum_stock }}</td>
                <td class="text-center">
                    @php
                        $isLow = $warehouseItem->stock <= $warehouseItem->minimum_stock;
                    @endphp
                    @if($isLow)
                        <span class="badge badge-danger">Menipis</span>
                    @else
                        <span class="badge badge-success">Normal</span>
                    @endif
                </td>
                <td>{{ $warehouseItem->item->rack_location ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data stok barang</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name }} | Warehouse Management System
    </div>
</body>
</html>
