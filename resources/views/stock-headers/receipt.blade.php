@extends('layouts.app')

@section('title', 'Tanda Terima - ' . $stockHeader->document_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Tanda Terima</h4>
                <p class="text-muted mb-0">{{ $stockHeader->document_number }}</p>
            </div>
            <div>
                <a href="{{ route('stock-headers.show', $stockHeader) }}" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                @if($stockHeader->hasCompleteSignatures())
                <a href="{{ route('stock-headers.pdf', $stockHeader) }}" class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-pdf me-1"></i> Download PDF
                </a>
                @endif
            </div>
        </div>

        @if($stockHeader->isReceiptLocked())
        <div class="alert alert-info">
            <i class="bi bi-lock me-2"></i>
            <strong>Tanda terima sudah dikunci.</strong> Tidak dapat mengubah tanda tangan.
        </div>
        @endif

        <!-- Receipt Preview -->
        <div class="card mb-4" id="receiptPreview">
            <div class="card-body p-4" style="background: white; color: #000;">
                <!-- Company Header -->
                <div class="text-center mb-4 pb-3 border-bottom">
                    <h4 class="fw-bold mb-1">TANDA TERIMA</h4>
                    <p class="mb-0">{{ $stockHeader->type_label }}</p>
                </div>

                <!-- Transaction Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm" style="color: #000;">
                            <tr>
                                <td width="140"><strong>No. Dokumen</strong></td>
                                <td>: {{ $stockHeader->document_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td>: {{ $stockHeader->transaction_date->format('d F Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jenis</strong></td>
                                <td>: <span class="badge {{ $stockHeader->type_badge_class }}">{{ $stockHeader->type_label }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm" style="color: #000;">
                            <tr>
                                <td width="120"><strong>Petugas</strong></td>
                                <td>: {{ $stockHeader->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Item</strong></td>
                                <td>: {{ $stockHeader->total_items }} barang</td>
                            </tr>
                            <tr>
                                <td><strong>Catatan</strong></td>
                                <td>: {{ $stockHeader->notes ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered" style="color: #000;">
                        <thead class="table-light">
                            <tr>
                                <th width="40">#</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th class="text-center">Qty</th>
                                <th>Satuan</th>
                                <th>Stok Sebelum</th>
                                <th>Stok Sesudah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockHeader->transactions as $index => $transaction)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $transaction->item->code }}</code></td>
                                <td>{{ $transaction->item->name }}</td>
                                <td class="text-center fw-bold {{ $stockHeader->type === 'in' ? 'text-success' : 'text-danger' }}">
                                    {{ $stockHeader->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                                </td>
                                <td>{{ $transaction->item->unit->abbreviation }}</td>
                                <td>{{ $transaction->stock_before }}</td>
                                <td>{{ $transaction->stock_after }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="text-center {{ $stockHeader->type === 'in' ? 'text-success' : 'text-danger' }}">
                                    {{ $stockHeader->type === 'in' ? '+' : '-' }}{{ $stockHeader->total_quantity }}
                                </th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Signatures Section -->
                <div class="row mt-5">
                    <div class="col-6 text-center">
                        <p class="mb-2"><strong>{{ $stockHeader->type === 'in' ? 'Pengirim' : 'Penerima' }}</strong></p>
                        <div class="border rounded p-2 mb-2" style="min-height: 120px; background: #f8f9fa;">
                            @if($stockHeader->sender_signature)
                            <img src="{{ $stockHeader->sender_signature_url }}" alt="Tanda Tangan" style="max-height: 100px; max-width: 100%;">
                            @else
                            <span class="text-muted small">Belum ditandatangani</span>
                            @endif
                        </div>
                        <p class="mb-0 border-top pt-2">{{ $stockHeader->sender_name ?: '.........................' }}</p>
                    </div>
                    <div class="col-6 text-center">
                        <p class="mb-2"><strong>{{ $stockHeader->type === 'in' ? 'Penerima (Gudang)' : 'Pengirim (Gudang)' }}</strong></p>
                        <div class="border rounded p-2 mb-2" style="min-height: 120px; background: #f8f9fa;">
                            @if($stockHeader->receiver_signature)
                            <img src="{{ $stockHeader->receiver_signature_url }}" alt="Tanda Tangan" style="max-height: 100px; max-width: 100%;">
                            @else
                            <span class="text-muted small">Belum ditandatangani</span>
                            @endif
                        </div>
                        <p class="mb-0 border-top pt-2">{{ $stockHeader->receiver_name ?: '.........................' }}</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4 pt-3 border-top">
                    <small class="text-muted">Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</small>
                </div>
            </div>
        </div>

        <!-- Signature Form -->
        @if(!$stockHeader->isReceiptLocked())
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pen me-2"></i>Input Tanda Tangan
            </div>
            <div class="card-body">
                <form id="signatureForm">
                    @csrf
                    <div class="row g-4">
                        <!-- Sender Signature -->
                        <div class="col-md-6">
                            <div class="card h-100" style="background: var(--table-hover);">
                                <div class="card-body">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="bi bi-person me-1"></i>
                                        {{ $stockHeader->type === 'in' ? 'Pengirim' : 'Penerima' }}
                                    </h6>
                                    <div class="mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" name="sender_name" 
                                               value="{{ $stockHeader->sender_name }}" 
                                               placeholder="Masukkan nama lengkap">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tanda Tangan <small class="text-muted">(gambar di kotak)</small></label>
                                        <div class="border rounded" style="background: white;">
                                            <canvas id="senderSignature" style="width: 100%; height: 150px; cursor: crosshair;"></canvas>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="senderPad.clear()">
                                                <i class="bi bi-eraser"></i> Hapus
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="senderPad.undo()">
                                                <i class="bi bi-arrow-counterclockwise"></i> Undo
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">Atau Upload Gambar</label>
                                        <input type="file" class="form-control form-control-sm" id="senderUpload" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Receiver Signature -->
                        <div class="col-md-6">
                            <div class="card h-100" style="background: var(--table-hover);">
                                <div class="card-body">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="bi bi-person-check me-1"></i>
                                        {{ $stockHeader->type === 'in' ? 'Penerima (Gudang)' : 'Pengirim (Gudang)' }}
                                    </h6>
                                    <div class="mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" name="receiver_name" 
                                               value="{{ $stockHeader->receiver_name }}" 
                                               placeholder="Masukkan nama lengkap">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tanda Tangan <small class="text-muted">(gambar di kotak)</small></label>
                                        <div class="border rounded" style="background: white;">
                                            <canvas id="receiverSignature" style="width: 100%; height: 150px; cursor: crosshair;"></canvas>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="receiverPad.clear()">
                                                <i class="bi bi-eraser"></i> Hapus
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="receiverPad.undo()">
                                                <i class="bi bi-arrow-counterclockwise"></i> Undo
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">Atau Upload Gambar</label>
                                        <input type="file" class="form-control form-control-sm" id="receiverUpload" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="lockReceipt" name="lock_receipt">
                            <label class="form-check-label" for="lockReceipt">
                                <strong>Kunci tanda terima</strong>
                                <small class="text-muted d-block">Setelah dikunci, tanda tangan tidak bisa diubah</small>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary" id="saveBtn">
                            <i class="bi bi-check-lg me-1"></i> Simpan Tanda Tangan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/signature-pad.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const senderCanvas = document.getElementById('senderSignature');
    const receiverCanvas = document.getElementById('receiverSignature');
    
    if (senderCanvas && receiverCanvas) {
        window.senderPad = new SignaturePad(senderCanvas);
        window.receiverPad = new SignaturePad(receiverCanvas);
    }

    const senderUpload = document.getElementById('senderUpload');
    const receiverUpload = document.getElementById('receiverUpload');

    if (senderUpload) {
        senderUpload.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                senderPad.uploadedFile = e.target.files[0];
            }
        });
    }

    if (receiverUpload) {
        receiverUpload.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                receiverPad.uploadedFile = e.target.files[0];
            }
        });
    }

    const form = document.getElementById('signatureForm');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Menyimpan...';

            const formData = new FormData(form);
            
            // Helper function to convert canvas to blob
            function canvasToBlob(signaturePad) {
                return new Promise((resolve) => {
                    if (!signaturePad || signaturePad.isEmpty()) {
                        resolve(null);
                        return;
                    }
                    signaturePad.toBlob((blob) => {
                        resolve(blob);
                    });
                });
            }
            
            try {
                // Get sender signature
                if (window.senderPad && !window.senderPad.isEmpty()) {
                    const senderBlob = await canvasToBlob(window.senderPad);
                    if (senderBlob) {
                        const senderFile = new File([senderBlob], 'sender_signature.png', { type: 'image/png' });
                        formData.append('sender_signature', senderFile);
                    }
                } else if (window.senderPad && window.senderPad.uploadedFile) {
                    formData.append('sender_signature', window.senderPad.uploadedFile);
                }

                // Get receiver signature
                if (window.receiverPad && !window.receiverPad.isEmpty()) {
                    const receiverBlob = await canvasToBlob(window.receiverPad);
                    if (receiverBlob) {
                        const receiverFile = new File([receiverBlob], 'receiver_signature.png', { type: 'image/png' });
                        formData.append('receiver_signature', receiverFile);
                    }
                } else if (window.receiverPad && window.receiverPad.uploadedFile) {
                    formData.append('receiver_signature', window.receiverPad.uploadedFile);
                }

                const response = await fetch('{{ route("stock-headers.signatures.save", $stockHeader) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('Tanda tangan berhasil disimpan!');
                    window.location.reload();
                } else {
                    alert(result.message || 'Gagal menyimpan tanda tangan');
                }
            } catch (error) {
                console.error('Error saving signature:', error);
                alert('Terjadi kesalahan saat menyimpan: ' + error.message);
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan Tanda Tangan';
            }
        });
    }
});
</script>
@endpush
