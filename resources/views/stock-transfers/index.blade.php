@extends('layouts.app')

@section('title', 'Transfer Stok')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Transfer Stok Antar Gudang</h4>
        <p class="text-muted mb-0">Kelola perpindahan stok antar gudang</p>
    </div>
    @if(!auth()->user()->isOwner())
    <a href="{{ route('stock-transfers.create') }}" class="btn btn-primary">
        <i class="bi bi-arrow-left-right me-1"></i> Transfer Baru
    </a>
    @endif
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('stock-transfers.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="warehouse_id" class="form-select">
                    <option value="">Semua Gudang</option>
                    @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                        {{ $warehouse->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nomor Transfer</th>
                        <th>Gudang Asal</th>
                        <th>Gudang Tujuan</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                    <tr>
                        <td class="fw-semibold">{{ $transfer->transfer_number }}</td>
                        <td>{{ $transfer->sourceWarehouse->name }}</td>
                        <td>{{ $transfer->destinationWarehouse->name }}</td>
                        <td>
                            <span class="badge bg-success">Selesai</span>
                        </td>
                        <td>{{ $transfer->user->name }}</td>
                        <td>{{ $transfer->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('stock-transfers.show', $transfer) }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-arrow-left-right fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada data transfer stok</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transfers->hasPages())
    <div class="card-footer">
        {{ $transfers->links() }}
    </div>
    @endif
</div>
@endsection
