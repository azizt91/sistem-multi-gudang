@extends('layouts.app')

@section('title', 'Edit Satuan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil me-2"></i>Edit Satuan
            </div>
            <div class="card-body">
                <form action="{{ route('units.update', $unit) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Satuan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $unit->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="abbreviation" class="form-label">Singkatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('abbreviation') is-invalid @enderror" id="abbreviation" name="abbreviation" value="{{ old('abbreviation', $unit->abbreviation) }}" required>
                        @error('abbreviation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('units.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
