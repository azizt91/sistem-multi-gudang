@extends('layouts.app')

@section('title', 'Daftar Satuan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Daftar Satuan</h4>
        <p class="text-muted mb-0">Kelola satuan barang</p>
    </div>
    <a href="{{ route('units.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Satuan
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama Satuan</th>
                        <th>Singkatan</th>
                        <th class="text-center">Jumlah Barang</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                    <tr>
                        <td class="fw-semibold">{{ $unit->name }}</td>
                        <td><code>{{ $unit->abbreviation }}</code></td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $unit->items_count }}</span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('units.edit', $unit) }}">
                                            <i class="bi bi-pencil text-primary me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('units.destroy', $unit) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus satuan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada satuan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($units->hasPages())
    <div class="card-footer">
        {{ $units->links() }}
    </div>
    @endif
</div>
@endsection
