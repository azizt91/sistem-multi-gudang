@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Daftar Transaksi</h4>
        <p class="text-muted mb-0">Dokumen transaksi stok masuk dan keluar</p>
    </div>
    @if(auth()->user()->canCreateTransaction())
    <div class="btn-group">
        <a href="{{ route('stock-headers.create-in') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i> Stok Masuk
        </a>
        <a href="{{ route('stock-headers.create-out') }}" class="btn btn-danger">
            <i class="bi bi-dash-lg me-1"></i> Stok Keluar
        </a>
    </div>
    @endif
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('stock-headers.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Jenis Transaksi</label>
                <select name="type" class="form-select">
                    <option value="">Semua</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Masuk</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Keluar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Transactions Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Dokumen</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th class="text-center">Jumlah Item</th>
                        <th class="text-center">Total Qty</th>
                        <th>Petugas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($headers as $header)
                    <tr>
                        <td>
                            <a href="{{ route('stock-headers.show', $header) }}" class="fw-semibold text-decoration-none">
                                {{ $header->document_number }}
                            </a>
                        </td>
                        <td>{{ $header->transaction_date->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge {{ $header->type_badge_class }}">
                                {{ $header->type_label }}
                            </span>
                        </td>
                        <td class="text-center">{{ $header->total_items }}</td>
                        <td class="text-center fw-semibold {{ $header->type === 'in' ? 'text-success' : 'text-danger' }}">
                            {{ $header->type === 'in' ? '+' : '-' }}{{ $header->total_quantity }}
                        </td>
                        <td>{{ $header->user->name }}</td>
                        <td>
                            @if($header->isReceiptLocked())
                            <span class="badge bg-info"><i class="bi bi-lock me-1"></i>Dikunci</span>
                            @elseif($header->hasCompleteSignatures())
                            <span class="badge bg-success"><i class="bi bi-check me-1"></i>Ditandatangani</span>
                            @else
                            <span class="badge bg-secondary">Belum TTD</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('stock-headers.show', $header) }}">
                                            <i class="bi bi-eye text-info me-2"></i> Lihat Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('stock-headers.receipt', $header) }}">
                                            <i class="bi bi-file-earmark-text text-primary me-2"></i> Tanda Terima
                                        </a>
                                    </li>
                                    @if($header->hasCompleteSignatures())
                                    <li>
                                        <a class="dropdown-item" href="{{ route('stock-headers.pdf', $header) }}" target="_blank">
                                            <i class="bi bi-file-pdf text-danger me-2"></i> Download PDF
                                        </a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->isAdmin() && !$header->isReceiptLocked())
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('stock-headers.destroy', $header) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus transaksi ini? Stok akan dikembalikan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada transaksi ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($headers->hasPages())
    <div class="card-footer">
        {{ $headers->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
