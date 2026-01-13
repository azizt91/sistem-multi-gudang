@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2"></i>Tambah Barang Baru
            </div>
            <div class="card-body">
                <!-- Barcode Scanner Section -->
                <div class="mb-4 p-3 rounded border" style="background: var(--table-hover);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-semibold mb-0">
                            <i class="bi bi-upc-scan me-2"></i>Scan Barcode Produk
                        </h6>
                        <button type="button" class="btn btn-primary btn-sm" id="toggleScannerBtn">
                            <i class="bi bi-camera-video me-1"></i> <span id="toggleBtnText">Mulai Scan</span>
                        </button>
                    </div>
                    
                    <!-- Scanner Container -->
                    <div id="scannerContainer" style="display: none;">
                        <div class="row">
                            <div class="col-md-8">
                                <div id="reader" style="width: 100%; border-radius: 0.5rem; overflow: hidden;"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded h-100" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                                    <h6 class="text-muted mb-3"><i class="bi bi-info-circle me-1"></i> Petunjuk</h6>
                                    <ul class="small mb-0">
                                        <li>Arahkan kamera ke barcode produk</li>
                                        <li>Pastikan barcode terlihat jelas</li>
                                        <li>Jaga jarak 10-20cm dari barcode</li>
                                        <li>Hasil scan akan otomatis mengisi kode barang</li>
                                    </ul>
                                    <hr>
                                    <p class="small text-muted mb-0">
                                        <strong>Format didukung:</strong><br>
                                        Code128, EAN-13, UPC, Code39
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Scan Result Preview -->
                        <div id="scanPreview" class="mt-3" style="display: none;">
                            <div class="alert mb-0" id="scanResultAlert">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong id="scanResultTitle"></strong>
                                        <p class="mb-0 small" id="scanResultMessage"></p>
                                    </div>
                                    <button type="button" class="btn btn-sm" id="scanResultAction"></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Input Option -->
                    <div id="manualInputSection" class="mt-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-keyboard"></i></span>
                            <input type="text" class="form-control" id="manualBarcodeInput" placeholder="Atau ketik kode barcode manual...">
                            <button type="button" class="btn btn-outline-primary" id="checkBarcodeBtn">
                                <i class="bi bi-search"></i> Cek
                            </button>
                        </div>
                        <div id="manualCheckResult" class="mt-2"></div>
                    </div>
                </div>

                <form action="{{ route('items.store') }}" method="POST" id="itemForm">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="code" class="form-label">Kode Barang <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                                <span class="input-group-text" id="codeStatus"></span>
                            </div>
                            <small class="text-muted">Kode ini akan digunakan untuk generate barcode</small>
                            @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->abbreviation }})
                                </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="stock" class="form-label">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required>
                            @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="minimum_stock" class="form-label">Minimum Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', 0) }}" min="0" required>
                            <small class="text-muted">Notifikasi jika stok dibawah ini</small>
                            @error('minimum_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="rack_location" class="form-label">Lokasi Rak</label>
                            <input type="text" class="form-control @error('rack_location') is-invalid @enderror" id="rack_location" name="rack_location" value="{{ old('rack_location') }}" placeholder="Contoh: A-01">
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
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-lg me-1"></i> Simpan Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Barcode Exists Modal -->
<div class="modal fade" id="barcodeExistsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Barang Sudah Terdaftar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Kode barcode <strong id="existingCode"></strong> sudah terdaftar:</p>
                <div class="alert alert-info mb-0">
                    <strong id="existingItemName"></strong>
                    <br>
                    <small>Stok saat ini: <span id="existingItemStock"></span></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" class="btn btn-primary" id="viewExistingItemBtn">
                    <i class="bi bi-eye me-1"></i> Lihat Barang
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let html5QrcodeScanner = null;
    let isScanning = false;

    const toggleScannerBtn = document.getElementById('toggleScannerBtn');
    const toggleBtnText = document.getElementById('toggleBtnText');
    const scannerContainer = document.getElementById('scannerContainer');
    const scanPreview = document.getElementById('scanPreview');
    const scanResultAlert = document.getElementById('scanResultAlert');
    const scanResultTitle = document.getElementById('scanResultTitle');
    const scanResultMessage = document.getElementById('scanResultMessage');
    const scanResultAction = document.getElementById('scanResultAction');
    const codeInput = document.getElementById('code');
    const codeStatus = document.getElementById('codeStatus');
    const manualBarcodeInput = document.getElementById('manualBarcodeInput');
    const checkBarcodeBtn = document.getElementById('checkBarcodeBtn');
    const manualCheckResult = document.getElementById('manualCheckResult');

    // Toggle Scanner
    toggleScannerBtn.addEventListener('click', function() {
        if (isScanning) {
            stopScanner();
        } else {
            startScanner();
        }
    });

    function startScanner() {
        scannerContainer.style.display = 'block';
        
        html5QrcodeScanner = new Html5Qrcode("reader");
        
        const config = {
            fps: 10,
            qrbox: { width: 300, height: 150 },
            formatsToSupport: [
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.QR_CODE
            ]
        };
        
        // Attempt 1: Back Camera
        html5QrcodeScanner.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanError
        ).then(() => {
            handleScanSuccessStart();
        }).catch(err => {
            console.warn("Back camera failed, trying front...", err);
            // Attempt 2: Front/User Camera
            html5QrcodeScanner.start(
                { facingMode: "user" },
                config,
                onScanSuccess,
                onScanError
            ).then(() => {
                handleScanSuccessStart();
            }).catch(err2 => {
                showScanError('Tidak dapat mengakses kamera: ' + err2);
            });
        });
    }

    function handleScanSuccessStart() {
        isScanning = true;
        toggleBtnText.textContent = 'Stop Scan';
        toggleScannerBtn.classList.remove('btn-primary');
        toggleScannerBtn.classList.add('btn-danger');
    }

    // Cleanup Listeners
    window.addEventListener('beforeunload', function() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().catch(err => {});
        }
    });

    document.addEventListener('visibilitychange', function() {
        if (document.hidden && isScanning) {
            stopScanner();
        }
    });

    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                isScanning = false;
                toggleBtnText.textContent = 'Mulai Scan';
                toggleScannerBtn.classList.remove('btn-danger');
                toggleScannerBtn.classList.add('btn-primary');
                scannerContainer.style.display = 'none';
                scanPreview.style.display = 'none';
            }).catch(err => console.error('Error stopping scanner:', err));
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Play beep sound
        playBeep();
        
        // Stop scanner temporarily
        if (html5QrcodeScanner) {
            html5QrcodeScanner.pause();
        }

        // Check if code already exists
        checkCodeExists(decodedText);
    }

    function onScanError(error) {
        // Ignore scan errors during continuous scanning
    }

    function playBeep() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            oscillator.frequency.value = 1000;
            oscillator.type = 'sine';
            gainNode.gain.value = 0.3;
            oscillator.start();
            setTimeout(() => oscillator.stop(), 100);
        } catch (e) {
            console.log('Audio not supported');
        }
    }

    function checkCodeExists(code) {
        fetch('{{ route("items.find-by-code") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            scanPreview.style.display = 'block';
            
            if (data.success) {
                // Code exists - show warning
                showCodeExists(code, data.item);
            } else {
                // Code is available - use it
                showCodeAvailable(code);
            }
        })
        .catch(error => {
            showCodeAvailable(code); // Assume available if check fails
        });
    }

    function showCodeExists(code, item) {
        scanResultAlert.className = 'alert alert-warning mb-0';
        scanResultTitle.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Kode Sudah Terdaftar';
        scanResultMessage.innerHTML = `<strong>${code}</strong> sudah digunakan oleh: ${item.name} (Stok: ${item.stock})`;
        scanResultAction.className = 'btn btn-sm btn-warning';
        scanResultAction.innerHTML = '<i class="bi bi-arrow-repeat"></i> Scan Ulang';
        scanResultAction.onclick = function() {
            scanPreview.style.display = 'none';
            if (html5QrcodeScanner) {
                html5QrcodeScanner.resume();
            }
        };

        // Update code input with warning
        codeInput.value = code;
        codeInput.classList.add('is-invalid');
        codeStatus.innerHTML = '<i class="bi bi-x-circle text-danger"></i>';

        // Show modal
        document.getElementById('existingCode').textContent = code;
        document.getElementById('existingItemName').textContent = item.name;
        document.getElementById('existingItemStock').textContent = item.stock;
        document.getElementById('viewExistingItemBtn').href = `/items/${item.id}`;
        
        const modal = new bootstrap.Modal(document.getElementById('barcodeExistsModal'));
        modal.show();
    }

    function showCodeAvailable(code) {
        scanResultAlert.className = 'alert alert-success mb-0';
        scanResultTitle.innerHTML = '<i class="bi bi-check-circle me-1"></i> Kode Tersedia';
        scanResultMessage.innerHTML = `Kode <strong>${code}</strong> dapat digunakan untuk barang baru`;
        scanResultAction.className = 'btn btn-sm btn-success';
        scanResultAction.innerHTML = '<i class="bi bi-check"></i> Gunakan';
        scanResultAction.onclick = function() {
            codeInput.value = code;
            codeInput.classList.remove('is-invalid');
            codeInput.classList.add('is-valid');
            codeStatus.innerHTML = '<i class="bi bi-check-circle text-success"></i>';
            stopScanner();
            document.getElementById('name').focus();
        };

        // Auto-fill code
        codeInput.value = code;
        codeInput.classList.remove('is-invalid');
        codeInput.classList.add('is-valid');
        codeStatus.innerHTML = '<i class="bi bi-check-circle text-success"></i>';
    }

    function showScanError(message) {
        scanPreview.style.display = 'block';
        scanResultAlert.className = 'alert alert-danger mb-0';
        scanResultTitle.innerHTML = '<i class="bi bi-x-circle me-1"></i> Error';
        scanResultMessage.textContent = message;
        scanResultAction.style.display = 'none';
    }

    // Manual barcode check
    checkBarcodeBtn.addEventListener('click', function() {
        const code = manualBarcodeInput.value.trim();
        if (!code) return;
        
        checkBarcodeBtn.disabled = true;
        checkBarcodeBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        
        fetch('{{ route("items.find-by-code") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            checkBarcodeBtn.disabled = false;
            checkBarcodeBtn.innerHTML = '<i class="bi bi-search"></i> Cek';
            
            if (data.success) {
                manualCheckResult.innerHTML = `
                    <div class="alert alert-warning py-2 mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i> 
                        Kode sudah terdaftar: <strong>${data.item.name}</strong>
                    </div>`;
                codeInput.classList.add('is-invalid');
                codeStatus.innerHTML = '<i class="bi bi-x-circle text-danger"></i>';
            } else {
                manualCheckResult.innerHTML = `
                    <div class="alert alert-success py-2 mb-0">
                        <i class="bi bi-check-circle me-1"></i> 
                        Kode tersedia dan dapat digunakan
                        <button type="button" class="btn btn-sm btn-success ms-2" id="useManualCodeBtn">Gunakan</button>
                    </div>`;
                
                document.getElementById('useManualCodeBtn').addEventListener('click', function() {
                    codeInput.value = code;
                    codeInput.classList.remove('is-invalid');
                    codeInput.classList.add('is-valid');
                    codeStatus.innerHTML = '<i class="bi bi-check-circle text-success"></i>';
                    manualBarcodeInput.value = '';
                    manualCheckResult.innerHTML = '';
                    document.getElementById('name').focus();
                });
            }
        })
        .catch(error => {
            checkBarcodeBtn.disabled = false;
            checkBarcodeBtn.innerHTML = '<i class="bi bi-search"></i> Cek';
            manualCheckResult.innerHTML = `
                <div class="alert alert-danger py-2 mb-0">
                    <i class="bi bi-x-circle me-1"></i> Terjadi kesalahan saat memeriksa kode
                </div>`;
        });
    });

    // Enter key for manual input
    manualBarcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            checkBarcodeBtn.click();
        }
    });

    // Validate code on blur
    codeInput.addEventListener('blur', function() {
        const code = this.value.trim();
        if (!code) return;
        
        fetch('{{ route("items.find-by-code") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                codeInput.classList.add('is-invalid');
                codeInput.classList.remove('is-valid');
                codeStatus.innerHTML = '<i class="bi bi-x-circle text-danger" title="Kode sudah digunakan"></i>';
            } else {
                codeInput.classList.remove('is-invalid');
                codeInput.classList.add('is-valid');
                codeStatus.innerHTML = '<i class="bi bi-check-circle text-success" title="Kode tersedia"></i>';
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
    #reader {
        border: 3px solid var(--primary);
        border-radius: 0.5rem;
    }
    
    #reader video {
        border-radius: 0.5rem;
    }

    #reader__scan_region {
        background: #000;
    }

    #reader__dashboard_section {
        padding: 10px !important;
    }

    #reader__dashboard_section_csr button {
        background: var(--primary) !important;
        border: none !important;
        padding: 8px 16px !important;
        border-radius: 0.375rem !important;
    }

    #reader__dashboard_section_csr span {
        font-size: 0.875rem !important;
    }
</style>
@endpush
