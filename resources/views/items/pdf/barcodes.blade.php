<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Barcode - {{ now()->format('d/m/Y H:i') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
        }

        .page {
            padding: 10mm;
        }

        .header {
            text-align: center;
            padding-bottom: 5mm;
            margin-bottom: 5mm;
            border-bottom: 1px solid #ddd;
        }

        .header h1 {
            font-size: 14px;
            margin-bottom: 2mm;
        }

        .header .meta {
            font-size: 9px;
            color: #666;
        }

        .barcode-grid {
            display: table;
            width: 100%;
        }

        .barcode-row {
            display: table-row;
        }

        .barcode-cell {
            display: table-cell;
            width: 33.33%;
            padding: 3mm;
            vertical-align: top;
        }

        .barcode-item {
            border: 1px solid #ccc;
            padding: 3mm;
            text-align: center;
            height: auto;
            page-break-inside: avoid;
        }

        .barcode-item .name {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 2mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 55mm;
        }

        .barcode-item .barcode-img {
            margin: 2mm 0;
        }

        .barcode-item .barcode-img img {
            max-width: 50mm;
            height: 15mm;
        }

        .barcode-item .code {
            font-size: 10px;
            font-weight: bold;
            font-family: monospace;
            letter-spacing: 1px;
        }

        .barcode-item .category {
            font-size: 7px;
            color: #666;
            margin-top: 1mm;
        }

        .footer {
            margin-top: 5mm;
            padding-top: 3mm;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #666;
        }

        @media print {
            .page {
                padding: 5mm;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <h1>DAFTAR BARCODE BARANG</h1>
            <div class="meta">
                Dicetak: {{ now()->format('d/m/Y H:i:s') }} | 
                Total: {{ $items->count() }} barang
            </div>
        </div>

        <!-- Barcode Grid - 3 columns -->
        <div class="barcode-grid">
            @foreach($items->chunk(3) as $chunk)
            <div class="barcode-row">
                @foreach($chunk as $item)
                <div class="barcode-cell">
                    @for($i = 0; $i < $quantity; $i++)
                    <div class="barcode-item">
                        <div class="name">{{ Str::limit($item->name, 25) }}</div>
                        <div class="barcode-img">
                            <img src="{{ $item->generateBarcode() }}" alt="Barcode">
                        </div>
                        <div class="code">{{ $item->code }}</div>
                        <div class="category">{{ $item->category->name }}</div>
                    </div>
                    @endfor
                </div>
                @endforeach
                @for($j = $chunk->count(); $j < 3; $j++)
                <div class="barcode-cell"></div>
                @endfor
            </div>
            @endforeach
        </div>

        <!-- Footer -->
        <div class="footer">
            Warehouse Management System | {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
