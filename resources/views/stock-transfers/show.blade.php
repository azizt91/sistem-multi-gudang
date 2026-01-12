@extends('layouts.app')

@section('title', 'Detail Transfer')

@section('content')
<div class="mb-4">
    <a href="{{ route('stock-transfers.index') }}" class="btn btn-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
    
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h4 class="mb-1">Detail Transfer Stok</h4>
            <p class="text-muted text-monospace">{{ $stockTransfer->transfer_number }}</p>
        </div>
        <div>
            <span class="badge bg-success fs-6">Selesai</span>
        </div>
    </div>
</div>

<div class="row">
    <!-- Transfer Info -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header fw-bold">Informasi Transfer</div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted" style="width: 120px">Tanggal</td>
                        <td class="fw-bold">{{ $stockTransfer->created_at->format('d F Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Oleh User</td>
                        <td>{{ $stockTransfer->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Catatan</td>
                        <td>{{ $stockTransfer->notes ?? '-' }}</td>
                    </tr>
                </table>

                <hr>

                <div class="mb-3">
                    <small class="text-muted d-block uppercase tracking-wide">Gudang Asal (Keluar)</small>
                    <div class="fw-bold fs-5 text-danger">{{ $stockTransfer->sourceWarehouse->name }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-center text-muted my-2">
                        <i class="bi bi-arrow-down fs-4"></i>
                    </div>
                </div>
                <div>
                    <small class="text-muted d-block uppercase tracking-wide">Gudang Tujuan (Masuk)</small>
                    <div class="fw-bold fs-5 text-success">{{ $stockTransfer->destinationWarehouse->name }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Documents -->
    <div class="col-md-8 mb-4">
        <div class="card mb-4">
            <div class="card-header fw-bold">Dokumen Terkait</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tipe</th>
                                <th>No. Dokumen</th>
                                <th>Gudang</th>
                                <th>Total Item</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockTransfer->stockHeaders as $header)
                            <tr>
                                <td>
                                    <span class="badge {{ $header->type_badge_class }}">
                                        {{ $header->type_label }}
                                    </span>
                                </td>
                                <td class="font-monospace">{{ $header->document_number }}</td>
                                <td>{{ $header->warehouse->name }}</td>
                                <td>{{ $header->total_items }}</td>
                                <td>
                                    <a href="{{ route('stock-headers.show', $header) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-box-arrow-up-right"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Items Summary (Grouped from one of the headers, e.g. the first one) -->
         <div class="card">
            <div class="card-header fw-bold">Rincian Barang</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Use the first header to list items (since they are identical)
                                $refHeader = $stockTransfer->stockHeaders->first();
                            @endphp
                            
                            @if($refHeader)
                                @foreach($refHeader->transactions as $trx)
                                <tr>
                                    <td>{{ $trx->item->code }}</td>
                                    <td>{{ $trx->item->name }}</td>
                                    <td class="fw-bold">{{ $trx->quantity }}</td>
                                    <td>{{ $trx->item->unit->name }}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
