@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">{{ $stockHeader->document_number }}</h4>
                <p class="text-muted mb-0">{{ $stockHeader->type_label }} - {{ $stockHeader->transaction_date->format('d F Y, H:i') }}</p>
            </div>
            <div>
                <a href="{{ route('stock-headers.index') }}" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('stock-headers.receipt', $stockHeader) }}" class="btn btn-primary">
                    <i class="bi bi-file-earmark-text me-1"></i> Tanda Terima
                </a>
            </div>
        </div>

        <!-- Transaction Info -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi {{ $stockHeader->type === 'in' ? 'bi-box-arrow-in-down text-success' : 'bi-box-arrow-up text-danger' }} me-2"></i>
                    Informasi Transaksi
                </span>
                <span class="badge {{ $stockHeader->type_badge_class }}">{{ $stockHeader->type_label }}</span>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="140"><strong>No. Dokumen</strong></td>
                                <td>{{ $stockHeader->document_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td>{{ $stockHeader->transaction_date->format('d F Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Petugas</strong></td>
                                <td>{{ $stockHeader->user->name ?? 'User Terhapus' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Gudang</strong></td>
                                <td>{{ $stockHeader->warehouse->name ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="120"><strong>Total Item</strong></td>
                                <td>{{ $stockHeader->total_items }} barang</td>
                            </tr>
                            <tr>
                                <td><strong>Total Qty</strong></td>
                                <td class="fw-bold {{ $stockHeader->type === 'in' ? 'text-success' : 'text-danger' }}">
                                    {{ $stockHeader->type === 'in' ? '+' : '-' }}{{ $stockHeader->total_quantity }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>
                                    @if($stockHeader->isReceiptLocked())
                                    <span class="badge bg-info"><i class="bi bi-lock me-1"></i>Dikunci</span>
                                    @elseif($stockHeader->hasCompleteSignatures())
                                    <span class="badge bg-success"><i class="bi bi-check me-1"></i>Ditandatangani</span>
                                    @else
                                    <span class="badge bg-secondary">Belum TTD</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                @if($stockHeader->notes)
                <hr>
                <div>
                    <strong>Catatan:</strong>
                    <p class="mb-0 text-muted">{{ $stockHeader->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Items List -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-box-seam me-2"></i>Daftar Barang ({{ $stockHeader->total_items }} item)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th class="text-center">Qty</th>
                                <th>Satuan</th>
                                <th>Stok Sebelum</th>
                                <th>Stok Sesudah</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockHeader->transactions as $index => $transaction)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ optional($transaction->item)->code ?? 'N/A' }}</code></td>
                                <td>{{ optional($transaction->item)->name ?? 'Item Terhapus' }}</td>

                                <td class="text-center fw-bold {{ $stockHeader->type === 'in' ? 'text-success' : 'text-danger' }}">
                                    {{ $stockHeader->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                                </td>

                                <td>{{ optional(optional($transaction->item)->unit)->abbreviation ?? '-' }}</td>

                                <td>{{ $transaction->stock_before }}</td>
                                <td>{{ $transaction->stock_after }}</td>
                                <td>{{ $transaction->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="text-center {{ $stockHeader->type === 'in' ? 'text-success' : 'text-danger' }}">
                                    {{ $stockHeader->type === 'in' ? '+' : '-' }}{{ $stockHeader->total_quantity }}
                                </th>
                                <th colspan="4"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Receipt Status -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-file-earmark-text me-2"></i>Status Tanda Terima
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            @if($stockHeader->sender_signature)
                            <i class="bi bi-check-circle-fill text-success fs-4 me-2"></i>
                            @else
                            <i class="bi bi-circle text-muted fs-4 me-2"></i>
                            @endif
                            <div>
                                <strong>{{ $stockHeader->type === 'in' ? 'Pengirim' : 'Penerima' }}</strong>
                                <br>
                                <small class="text-muted">{{ $stockHeader->sender_name ?: 'Belum diisi' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            @if($stockHeader->receiver_signature)
                            <i class="bi bi-check-circle-fill text-success fs-4 me-2"></i>
                            @else
                            <i class="bi bi-circle text-muted fs-4 me-2"></i>
                            @endif
                            <div>
                                <strong>{{ $stockHeader->type === 'in' ? 'Penerima (Gudang)' : 'Pengirim (Gudang)' }}</strong>
                                <br>
                                <small class="text-muted">{{ $stockHeader->receiver_name ?: 'Belum diisi' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    @if(!$stockHeader->isReceiptLocked())
                    <a href="{{ route('stock-headers.receipt', $stockHeader) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pen me-1"></i> Input Tanda Tangan
                    </a>
                    @endif

                    @if($stockHeader->hasCompleteSignatures())
                    <a href="{{ route('stock-headers.pdf', $stockHeader) }}" class="btn btn-danger" target="_blank">
                        <i class="bi bi-file-pdf me-1"></i> Download PDF
                    </a>
                    @endif
                </div>

                @if($stockHeader->isReceiptLocked())
                <div class="alert alert-info mb-0 mt-3">
                    <i class="bi bi-lock me-2"></i>
                    Tanda terima sudah dikunci pada {{ $stockHeader->updated_at->format('d/m/Y H:i') }}.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
