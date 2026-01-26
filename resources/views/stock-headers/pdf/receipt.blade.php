<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tanda Terima - {{ $stockHeader->document_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px 25px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 13px;
            color: #666;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info-table .label {
            font-weight: bold;
            width: 110px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }

        .items-table th {
            background: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
        }

        .items-table .text-center {
            text-align: center;
        }

        .items-table .text-right {
            text-align: right;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .summary-row {
            background: #f8f9fa;
            font-weight: bold;
        }

        .signatures {
            width: 100%;
            margin-top: 30px;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 15px;
        }

        .signature-box {
            border: 1px solid #ccc;
            min-height: 70px;
            margin: 8px 0;
            padding: 5px;
        }

        .signature-box img {
            max-width: 140px;
            max-height: 60px;
        }

        .signature-name {
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 8px;
            min-width: 140px;
            display: inline-block;
            font-size: 10px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-danger {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @php
                $profile = \App\Models\CompanyProfile::first();
            @endphp
            @if($profile && $profile->logo_path && file_exists(public_path('storage/' . $profile->logo_path)))
                <img src="{{ public_path('storage/' . $profile->logo_path) }}" style="height: 40px; margin-bottom: 5px;">
            @endif
            <h1 style="margin-top: 5px;">{{ $profile->company_name ?? 'WMS' }}</h1>
            <div style="margin-bottom: 10px; font-size: 10px;">{{ $profile->address ?? '' }}</div>
            <h2 style="font-size: 16px; border-top: 1px solid #333; padding-top: 10px;">TANDA TERIMA</h2>
            <div class="subtitle">{{ $stockHeader->type_label }}</div>
        </div>

        <!-- Transaction Info -->
        <table class="info-table">
            <tr>
                <td class="label">No. Dokumen</td>
                <td>: {{ $stockHeader->document_number }}</td>
                <td class="label">Petugas</td>
                <td>: {{ $stockHeader->user->name }}</td>
            </tr>
            <tr>
                <td class="label">Gudang</td>
                <td>: {{ $stockHeader->warehouse->name ?? '-' }}</td>
                <td class="label">Jenis</td>
                <td>:
                    <span class="badge {{ $stockHeader->type === 'in' ? 'badge-success' : 'badge-danger' }}">
                        {{ $stockHeader->type_label }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label">Tanggal</td>
                <td>: {{ $stockHeader->transaction_date->format('d F Y, H:i') }}</td>
                <td class="label"></td>
                <td></td>
            </tr>

            @if($stockHeader->notes)
            <tr>
                <td class="label">Catatan</td>
                <td colspan="3">: {{ $stockHeader->notes }}</td>
            </tr>
            @endif
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" width="30">#</th>
                    <th width="80">Kode</th>
                    <th>Nama Barang</th>
                    <th class="text-center" width="50">Qty</th>
                    <th width="50">Satuan</th>
                    <th width="60">Stok Awal</th>
                    <th width="60">Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockHeader->transactions as $index => $transaction)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $transaction->item->code }}</td>
                    <td>{{ $transaction->item->name }}</td>
                    <td class="text-center {{ $stockHeader->type === 'in' ? 'text-success' : 'text-danger' }}">
                        <strong>{{ $stockHeader->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }}</strong>
                    </td>
                    <td>{{ $transaction->item->unit->abbreviation }}</td>
                    <td class="text-center">{{ $transaction->stock_before }}</td>
                    <td class="text-center">{{ $transaction->stock_after }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="summary-row">
                    <td colspan="3" class="text-right">Total ({{ $stockHeader->total_items }} item):</td>
                    <td class="text-center {{ $stockHeader->type === 'in' ? 'text-success' : 'text-danger' }}">
                        {{ $stockHeader->type === 'in' ? '+' : '-' }}{{ $stockHeader->total_quantity }}
                    </td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <table class="signatures">
            <tr>
                <td>
                    <strong>{{ $stockHeader->type === 'in' ? 'Pengirim' : 'Penerima' }}</strong>
                    <div class="signature-box">
                        @if($stockHeader->sender_signature && file_exists(public_path('storage/' . $stockHeader->sender_signature)))
                            <img src="{{ public_path('storage/' . $stockHeader->sender_signature) }}" alt="TTD">
                        @endif
                    </div>
                    <div class="signature-name">
                        {{ $stockHeader->sender_name ?: '.........................' }}
                    </div>
                </td>
                <td>
                    <strong>{{ $stockHeader->type === 'in' ? 'Penerima (Gudang)' : 'Pengirim (Gudang)' }}</strong>
                    <div class="signature-box">
                        @if($stockHeader->receiver_signature && file_exists(public_path('storage/' . $stockHeader->receiver_signature)))
                            <img src="{{ public_path('storage/' . $stockHeader->receiver_signature) }}" alt="TTD">
                        @endif
                    </div>
                    <div class="signature-name">
                        {{ $stockHeader->receiver_name ?: '.........................' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | Warehouse Management System
        </div>
    </div>
</body>
</html>
