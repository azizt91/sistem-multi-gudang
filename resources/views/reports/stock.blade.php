@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Laporan Stok</h4>
        <p class="text-muted mb-0">Status stok semua barang</p>
    </div>
    <div>
        <a href="{{ route('reports.stock.excel', request()->query()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('reports.stock') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Gudang</label>
                <select name="warehouse_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Gudang</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach(\App\Models\Category::orderBy('name')->get() as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                 <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="lowStock" {{ request('low_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label" for="lowStock">Hanya Stok Menipis</label>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                 <a href="{{ route('reports.stock') }}" class="btn btn-secondary w-100">Reset Filter</a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stats-card primary">
            <div class="stats-value">{{ number_format($summary['total_items']) }}</div>
            <div class="stats-label">Total Jenis Barang</div>
            <i class="bi bi-box-seam stats-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card info">
            <div class="stats-value">{{ number_format($summary['total_stock']) }}</div>
            <div class="stats-label">Total Stok</div>
            <i class="bi bi-boxes stats-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card warning">
            <div class="stats-value">{{ number_format($summary['low_stock_count']) }}</div>
            <div class="stats-label">Stok Menipis</div>
            <i class="bi bi-exclamation-triangle stats-icon"></i>
        </div>
    </div>
</div>

<!-- Stock Table -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul me-2"></i>Daftar Stok Barang
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Min</th>
                        <th>Satuan</th>
                        <th>Status</th>
                        <th>Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td><code>{{ $item->code }}</code></td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name }}</td>
                        <td class="text-center fw-bold {{ $item->isLowStock() ? 'text-danger' : 'text-success' }}">
                            {{ $item->stock }}
                        </td>
                        <td class="text-center">{{ $item->minimum_stock }}</td>
                        <td>{{ $item->unit->abbreviation }}</td>
                        <td>
                            @if($item->isLowStock())
                            <span class="badge bg-danger">Stok Menipis</span>
                            @else
                            <span class="badge bg-success">Normal</span>
                            @endif
                        </td>
                        <td>{{ $item->rack_location ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada barang</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
