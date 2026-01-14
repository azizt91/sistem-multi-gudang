@extends('layouts.app')

@section('title', 'Manajemen Gudang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Manajemen Gudang</h4>
        <p class="text-muted mb-0">Kelola data gudang dan lokasi</p>
    </div>
    <a href="{{ route('warehouses.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Gudang
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama Gudang</th>
                        <th>PIC</th>
                        <th>No. Telp</th>
                        <th>Kota / Regional</th>
                        <th>Alamat</th>
                        <th class="text-center">Total Barang</th>
                        <th class="text-center">Total Transaksi</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $warehouse)
                    <tr>
                        <td class="fw-semibold">{{ $warehouse->name }}</td>
                        <td>{{ $warehouse->pic ?? '-' }}</td>
                        <td>{{ $warehouse->phone ?? '-' }}</td>
                        <td>{{ $warehouse->city }}</td>
                        <td>{{ $warehouse->address ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $warehouse->warehouse_items_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $warehouse->stock_headers_count }}</span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('warehouses.edit', $warehouse) }}">
                                            <i class="bi bi-pencil text-primary me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        @if($warehouse->stock_headers_count > 0 || $warehouse->warehouse_items_count > 0)
                                            <button class="dropdown-item text-muted" disabled title="Gudang memiliki data terkait">
                                                <i class="bi bi-trash me-2"></i> Hapus
                                            </button>
                                        @else
                                        <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus gudang ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i> Hapus
                                            </button>
                                        </form>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-building fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada data gudang</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($warehouses->hasPages())
    <div class="card-footer">
        {{ $warehouses->links() }}
    </div>
    @endif
</div>
@endsection
