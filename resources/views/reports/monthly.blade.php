@extends('layouts.app')

@section('title', 'Laporan Bulanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Laporan Bulanan</h4>
        <p class="text-muted mb-0">Periode: {{ $startDate->format('F Y') }}</p>
    </div>
    <div>
        <a href="{{ route('reports.monthly.pdf', ['month' => $month, 'year' => $year, 'warehouse_id' => request('warehouse_id')]) }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
        <a href="{{ route('reports.monthly.excel', ['month' => $month, 'year' => $year, 'warehouse_id' => request('warehouse_id')]) }}" class="btn btn-success ms-2">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
    </div>
</div>

<!-- Month Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('reports.monthly') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Gudang</label>
                <select name="warehouse_id" class="form-select">
                    <option value="">Semua Gudang</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <select name="month" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <select name="year" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stats-card success">
            <div class="stats-value">{{ number_format($summary['total_in']) }}</div>
            <div class="stats-label">Total Stok Masuk</div>
            <i class="bi bi-box-arrow-in-down stats-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card danger">
            <div class="stats-value">{{ number_format($summary['total_out']) }}</div>
            <div class="stats-label">Total Stok Keluar</div>
            <i class="bi bi-box-arrow-up stats-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card primary">
            <div class="stats-value">{{ number_format($summary['transaction_count']) }}</div>
            <div class="stats-label">Total Transaksi</div>
            <i class="bi bi-receipt stats-icon"></i>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul me-2"></i>Detail Transaksi
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Barang</th>
                        <th>Jenis</th>
                        <th class="text-end">Qty</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->transaction_date->format('d/m/Y H:i') }}</td>
                        <td><code>{{ $transaction->item->code }}</code></td>
                        <td>{{ Str::limit($transaction->item->name, 30) }}</td>
                        <td>
                            <span class="badge {{ $transaction->type_badge_class }}">
                                {{ $transaction->type_label }}
                            </span>
                        </td>
                        <td class="text-end fw-semibold {{ $transaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                        </td>
                        <td>{{ $transaction->user->name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada transaksi pada bulan ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
