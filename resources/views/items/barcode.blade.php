<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode - {{ $item->code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f5f5;
        }
        .barcode-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .barcode-card img {
            max-width: 200px;
            margin-bottom: 10px;
        }
        .barcode-code {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .barcode-name {
            font-size: 12px;
            color: #666;
            max-width: 200px;
        }
        @media print {
            body {
                background: white;
            }
            .barcode-card {
                box-shadow: none;
                border: 1px dashed #ccc;
            }
            .no-print {
                display: none !important;
            }
        }
        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #4F46E5;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #4338CA;
        }
    </style>
</head>
<body>
    <div class="barcode-card">
        <img src="{{ $item->generateBarcode() }}" alt="Barcode">
        <div class="barcode-code">{{ $item->code }}</div>
        <div class="barcode-name">{{ $item->name }}</div>
    </div>

    <button class="print-btn no-print" onclick="window.print()">
        🖨️ Cetak Barcode
    </button>
</body>
</html>
