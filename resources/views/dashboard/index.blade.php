@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Dashboard</h4>
        <p class="text-muted mb-0">Overview status gudang</p>
    </div>
    <div>
        @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
        <form action="{{ route('dashboard') }}" method="GET" class="d-flex gap-2">
            <select name="warehouse_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Gudang</option>
                @foreach($warehouses as $w)
                    <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                        {{ $w->name }}
                    </option>
                @endforeach
            </select>
            <noscript><button type="submit" class="btn btn-primary btn-sm">Go</button></noscript>
        </form>
        @else
        <span class="badge bg-primary fs-6">
            <i class="bi bi-geo-alt-fill me-1"></i>
            {{ auth()->user()->warehouse->name ?? 'Gudang Belum Ditentukan' }}
        </span>
        @endif
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card primary">
            <div class="stats-value">{{ number_format($stats['total_items']) }}</div>
            <div class="stats-label">Total Barang</div>
            <i class="bi bi-box-seam stats-icon"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card success">
            <div class="stats-value">{{ number_format($stats['total_stock_in_today']) }}</div>
            <div class="stats-label">Stok Masuk Hari Ini</div>
            <i class="bi bi-box-arrow-in-down stats-icon"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card danger">
            <div class="stats-value">{{ number_format($stats['total_stock_out_today']) }}</div>
            <div class="stats-label">Stok Keluar Hari Ini</div>
            <i class="bi bi-box-arrow-up stats-icon"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card warning">
            <div class="stats-value">{{ number_format($stats['low_stock_count']) }}</div>
            <div class="stats-label">Stok Menipis</div>
            <i class="bi bi-exclamation-triangle stats-icon"></i>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart -->
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-graph-up me-2"></i>Transaksi 7 Hari Terakhir</span>
            </div>
            <div class="card-body">
                <canvas id="transactionChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Summary -->
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-calendar-month me-2"></i>Ringkasan Bulan Ini
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <div class="text-muted small">Total Stok Masuk</div>
                        <div class="h4 mb-0 text-success">+{{ number_format($stats['total_stock_in_month']) }}</div>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-arrow-down-circle text-success fs-4"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Total Stok Keluar</div>
                        <div class="h4 mb-0 text-danger">-{{ number_format($stats['total_stock_out_month']) }}</div>
                    </div>
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-arrow-up-circle text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Low Stock Items -->
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-exclamation-triangle text-warning me-2"></i>Barang Stok Menipis</span>
                <a href="{{ route('items.index', ['low_stock' => 1]) }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($lowStockItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Min</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockItems->take(5) as $item)
                            <tr>
                                <td><code>{{ $item->code }}</code></td>
                                <td>{{ $item->name }}</td>
                                <td class="text-center">
                                    <span class="badge bg-danger low-stock-badge">{{ $item->stock }}</span>
                                </td>
                                <td class="text-center">{{ $item->minimum_stock }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                    <p class="mt-2 mb-0">Semua stok aman!</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Transaksi Terakhir</span>
                <a href="{{ route('stock-headers.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($recentTransactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Barang</th>
                                <th>Jenis</th>
                                <th class="text-end">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $transaction)
                            <tr>
                                <td class="small">{{ $transaction->transaction_date->format('d/m H:i') }}</td>
                                <td>{{ Str::limit($transaction->item->name, 20) }}</td>
                                <td>
                                    <span class="badge {{ $transaction->type_badge_class }}">
                                        {{ $transaction->type_label }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold {{ $transaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2 mb-0">Belum ada transaksi</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
@if(auth()->user()->canCreateTransaction())
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Aksi Cepat</h6>
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <a href="{{ route('stock-headers.create-in') }}" class="btn btn-success w-100 py-3">
                            <i class="bi bi-box-arrow-in-down fs-4 d-block mb-1"></i>
                            Stok Masuk
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('stock-headers.create-out') }}" class="btn btn-danger w-100 py-3">
                            <i class="bi bi-box-arrow-up fs-4 d-block mb-1"></i>
                            Stok Keluar
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('scanner.index') }}" class="btn btn-primary w-100 py-3">
                            <i class="bi bi-upc-scan fs-4 d-block mb-1"></i>
                            Scan Barcode
                        </a>
                    </div>
                    @if(auth()->user()->isAdmin())
                    <div class="col-md-3 col-6">
                        <a href="{{ route('items.create') }}" class="btn btn-info w-100 py-3 text-white">
                            <i class="bi bi-plus-circle fs-4 d-block mb-1"></i>
                            Tambah Barang
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('transactionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                {
                    label: 'Stok Masuk',
                    data: @json($chartData['stockIn']),
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Stok Keluar',
                    data: @json($chartData['stockOut']),
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
