@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profil Perusahaan</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pengaturan Profil</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('company-profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name', $profile->company_name) }}" required>
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $profile->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $profile->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $profile->website) }}" placeholder="https://example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo Perusahaan</label>
                            @if($profile->logo_url)
                                <div class="mb-2">
                                    <img src="{{ $profile->logo_url }}" alt="Logo" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                            <div class="form-text">Format: PNG, JPG, JPEG (Max 2MB). Digunakan pada header dokumen.</div>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Preview Kop Surat</h6>
                </div>
                <div class="card-body border">
                    <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                        @if($profile->logo_url)
                            <img src="{{ $profile->logo_url }}" alt="Logo" style="height: 60px; margin-right: 15px;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px; margin-right: 15px;">
                                <i class="fas fa-building text-secondary"></i>
                            </div>
                        @endif
                        <div>
                            <h4 class="m-0 fw-bold">{{ $profile->company_name }}</h4>
                            @if($profile->address)
                                <p class="mb-0 small text-muted">{{ $profile->address }}</p>
                            @endif
                            <div class="small text-muted">
                                @if($profile->phone) <span>T: {{ $profile->phone }}</span> @endif
                                @if($profile->phone && $profile->email) | @endif
                                @if($profile->email) <span>E: {{ $profile->email }}</span> @endif
                                @if(($profile->phone || $profile->email) && $profile->website) | @endif
                                @if($profile->website) <span>W: {{ $profile->website }}</span> @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-center text-muted p-5">
                        <p>Konten Dokumen Akan Muncul Disini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
