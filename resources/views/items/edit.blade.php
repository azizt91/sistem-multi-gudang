@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil me-2"></i>Edit Barang: {{ $item->name }}
            </div>
            <div class="card-body">
                <form action="{{ route('items.update', $item) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if(isset($contextWarehouse))
                    <div class="alert alert-info border-info d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                        <div>
                            <strong>Mode Edit Spesifik Gudang</strong><br>
                            Anda sedang mengubah konfigurasi untuk <strong>{{ $contextWarehouse->name }}</strong>. 
                            Perubahan pada <em>Minimum Stok</em> hanya akan berlaku untuk gudang ini.
                        </div>
                    </div>
                    <input type="hidden" name="warehouse_id" value="{{ $contextWarehouse->id }}">
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="code" class="form-label">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $item->code) }}" required>
                            @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $item->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="unit_id" class="form-label">Satuan <span class="text-danger">*</span></label>
                            <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                <option value="">Pilih Satuan</option>
                                @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $item->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->abbreviation }})
                                </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Stok Saat Ini @if(isset($contextWarehouse)) <small class="text-muted">({{ $contextWarehouse->name }})</small> @endif</label>
                            <input type="text" class="form-control bg-light" value="{{ $item->stock }} {{ $item->unit->abbreviation }}" readonly>
                            <small class="text-muted">Stok hanya bisa diubah melalui transaksi</small>
                        </div>

                        <div class="col-md-6">
                            <label for="minimum_stock" class="form-label">
                                Minimum Stok 
                                @if(isset($contextWarehouse)) 
                                    <span class="badge bg-info text-dark ms-1">Khusus {{ $contextWarehouse->name }}</span>
                                @else
                                    <span class="badge bg-secondary ms-1">Global/Utama</span>
                                @endif
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock) }}" min="0" required>
                            @error('minimum_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="rack_location" class="form-label">Lokasi Rak</label>
                            <input type="text" class="form-control @error('rack_location') is-invalid @enderror" id="rack_location" name="rack_location" value="{{ old('rack_location', $item->rack_location) }}" placeholder="Contoh: A-01">
                            @error('rack_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('items.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
