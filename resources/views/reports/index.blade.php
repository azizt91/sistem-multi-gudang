@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Laporan Stok</h4>
    <p class="text-muted mb-0">Lihat dan export laporan stok dan transaksi</p>
</div>

<div class="row g-4">
    <!-- Daily Report -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                    <i class="bi bi-calendar-day fs-1 text-primary"></i>
                </div>
                <h5 class="card-title">Laporan Harian</h5>
                <p class="card-text text-muted">Lihat transaksi stok per hari</p>
                <a href="{{ route('reports.daily') }}" class="btn btn-primary">
                    <i class="bi bi-eye me-1"></i> Lihat Laporan
                </a>
            </div>
        </div>
    </div>

    <!-- Monthly Report -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="bg-success bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                    <i class="bi bi-calendar-month fs-1 text-success"></i>
                </div>
                <h5 class="card-title">Laporan Bulanan</h5>
                <p class="card-text text-muted">Lihat transaksi stok per bulan</p>
                <a href="{{ route('reports.monthly') }}" class="btn btn-success">
                    <i class="bi bi-eye me-1"></i> Lihat Laporan
                </a>
            </div>
        </div>
    </div>

    <!-- Stock Report -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="bg-info bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                    <i class="bi bi-box-seam fs-1 text-info"></i>
                </div>
                <h5 class="card-title">Laporan Stok</h5>
                <p class="card-text text-muted">Lihat status stok semua barang</p>
                <a href="{{ route('reports.stock') }}" class="btn btn-info text-white">
                    <i class="bi bi-eye me-1"></i> Lihat Laporan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Export -->
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-download me-2"></i>Export Cepat
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <h6>Export Laporan Stok</h6>
                <a href="{{ route('reports.stock.excel') }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                </a>
            </div>
            <div class="col-md-6">
                <h6>Export Transaksi Hari Ini</h6>
                <a href="{{ route('reports.daily.pdf', ['date' => now()->toDateString()]) }}" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
