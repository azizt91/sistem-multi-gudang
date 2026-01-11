@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <!-- Item Info Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Informasi Barang</span>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                @endif
            </div>
            <div class="card-body text-center">
                <!-- Barcode -->
                <div class="mb-3 p-3 bg-white border rounded">
                    <img src="{{ $item->generateBarcode() }}" alt="Barcode" class="img-fluid mb-2">
                    <p class="mb-0 fw-bold">{{ $item->code }}</p>
                </div>

                <h5 class="fw-bold">{{ $item->name }}</h5>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-secondary">{{ $item->category->name }}</span>
                    @if($item->isLowStock())
                    <span class="badge bg-danger">Stok Menipis</span>
                    @endif
                </div>

                <div class="row text-start">
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Stok Saat Ini</small>
                        <span class="fw-bold fs-4 {{ $item->isLowStock() ? 'text-danger' : 'text-success' }}">
                            {{ $item->stock }}
                        </span>
                        <span class="text-muted">{{ $item->unit->abbreviation }}</span>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Minimum Stok</small>
                        <span class="fw-bold fs-4">{{ $item->minimum_stock }}</span>
                        <span class="text-muted">{{ $item->unit->abbreviation }}</span>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Satuan</small>
                        <span class="fw-semibold">{{ $item->unit->name }}</span>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Lokasi Rak</small>
                        <span class="fw-semibold">{{ $item->rack_location ?? '-' }}</span>
                    </div>
                </div>

                <hr>

                <div class="d-grid gap-2">
                    <a href="{{ route('items.barcode', $item) }}" class="btn btn-outline-primary" target="_blank">
                        <i class="bi bi-printer me-1"></i> Cetak Barcode
                    </a>
                    @if(auth()->user()->canCreateTransaction())
                    <a href="{{ route('stock-headers.create-in') }}?item_id={{ $item->id }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i> Stok Masuk
                    </a>
                    <a href="{{ route('stock-headers.create-out') }}?item_id={{ $item->id }}" class="btn btn-danger">
                        <i class="bi bi-dash-circle me-1"></i> Stok Keluar
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Transaction History -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Riwayat Transaksi
            </div>
            <div class="card-body p-0">
                @if($item->stockTransactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th class="text-end">Qty</th>
                                <th>Stok Sebelum</th>
                                <th>Stok Sesudah</th>
                                <th>User</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->stockTransactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_date->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge {{ $transaction->type_badge_class }}">
                                        {{ $transaction->type_label }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold {{ $transaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                                </td>
                                <td>{{ $transaction->stock_before }}</td>
                                <td>{{ $transaction->stock_after }}</td>
                                <td>{{ $transaction->user->name }}</td>
                                <td>{{ $transaction->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2 mb-0">Belum ada riwayat transaksi</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
