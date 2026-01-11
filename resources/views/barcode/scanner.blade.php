@extends('layouts.app')

@section('title', 'Scan Barcode (Multi-Item)')

@section('content')
<div class="row">
    <!-- Scanner Column -->
    <div class="col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-upc-scan me-2"></i>Scan Barcode</span>
                <span class="badge bg-primary" id="modeBadge">Mode: MASUK</span>
            </div>
            <div class="card-body">
                <!-- Mode Switch -->
                <div class="mb-4 text-center">
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="scanMode" id="modeIn" value="in" checked>
                        <label class="btn btn-outline-success" for="modeIn"><i class="bi bi-box-arrow-in-down me-1"></i> Stok Masuk</label>

                        <input type="radio" class="btn-check" name="scanMode" id="modeOut" value="out">
                        <label class="btn btn-outline-danger" for="modeOut"><i class="bi bi-box-arrow-up me-1"></i> Stok Keluar</label>
                    </div>
                </div>

                <!-- Scanner Area -->
                <div class="mb-3">
                    <div id="reader" class="scanner-container mb-3" style="width: 100%; overflow: hidden; border-radius: 8px;"></div>
                    
                    <div class="text-center mb-3">
                        <button type="button" class="btn btn-primary w-100 mb-2" id="startScanBtn">
                            <i class="bi bi-camera-video me-1"></i> Mulai Kamera
                        </button>
                        <button type="button" class="btn btn-secondary w-100 mb-2" id="stopScanBtn" style="display: none;">
                            <i class="bi bi-stop-circle me-1"></i> Stop Kamera
                        </button>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-keyboard"></i></span>
                        <input type="text" class="form-control" id="manualInput" placeholder="Ketik kode barang...">
                        <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Last Scanned Info -->
                <div id="lastScannedInfo" class="alert alert-info py-2 small" style="display:none;">
                    <strong>Terakhir:</strong> <span id="lastScannedName">-</span>
                </div>
                
                <div id="errorAlert" class="alert alert-danger py-2 small" style="display:none;">
                    <i class="bi bi-exclamation-triangle me-1"></i> <span id="errorMessage"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Column -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cart3 me-2"></i>Keranjang Scan</span>
                <span class="badge bg-secondary" id="cartCount">0 Item</span>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="table-responsive flex-grow-1" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Barang</th>
                                <th style="width: 120px;">Qty</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody">
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                                    Belum ada barang discan
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 pt-3 border-top">
                    <!-- Global Notes -->
                    <div class="mb-3">
                        <label class="form-label small">Catatan Transaksi (Opsional)</label>
                        <textarea class="form-control form-control-sm" id="globalNotes" rows="2" placeholder="Contoh: Barang dari Supplier A"></textarea>
                    </div>

                    <button type="button" class="btn btn-primary w-100 btn-lg" id="processBtn" disabled>
                        <i class="bi bi-save me-2"></i> Proses Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for Submission -->
<form id="transactionForm" action="{{ route('stock-headers.store') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="type" id="formType">
    <input type="hidden" name="notes" id="formNotes">
    <div id="formItemsContainer"></div>
</form>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    let html5QrcodeScanner = null;
    let isProcessing = false;

    // Elements
    const modeRadios = document.getElementsByName('scanMode');
    const modeBadge = document.getElementById('modeBadge');
    const cartTableBody = document.getElementById('cartTableBody');
    const cartCount = document.getElementById('cartCount');
    const processBtn = document.getElementById('processBtn');
    const manualInput = document.getElementById('manualInput');
    const searchBtn = document.getElementById('searchBtn');
    const lastScannedInfo = document.getElementById('lastScannedInfo');
    const lastScannedName = document.getElementById('lastScannedName');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');

    // Beep sound
    const beep = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU');

    // --- Event Listeners ---

    // Mode Switch
    modeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateModeUI();
            validateCartStock(); // Re-validate stock if switching mode
        });
    });

    // Start Camera
    document.getElementById('startScanBtn').addEventListener('click', function() {
        if (html5QrcodeScanner) return; // Prevent multiple instances

        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        html5QrcodeScanner = new Html5Qrcode("reader");
        
        html5QrcodeScanner.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).then(() => {
            this.style.display = 'none';
            document.getElementById('stopScanBtn').style.display = 'inline-block';
        }).catch(err => {
            showError("Gagal akses kamera: " + err);
        });
    });

    // Stop Camera
    document.getElementById('stopScanBtn').addEventListener('click', stopCamera);

    // Manual Input
    manualInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            handleScan(this.value);
            this.value = '';
        }
    });

    searchBtn.addEventListener('click', function() {
        handleScan(manualInput.value);
        manualInput.value = '';
    });

    // Process Transaction
    processBtn.addEventListener('click', submitTransaction);


    // --- Functions ---

    function updateModeUI() {
        const mode = document.querySelector('input[name="scanMode"]:checked').value;
        if (mode === 'in') {
            modeBadge.textContent = 'Mode: MASUK';
            modeBadge.className = 'badge bg-success';
        } else {
            modeBadge.textContent = 'Mode: KELUAR';
            modeBadge.className = 'badge bg-danger';
        }
    }

    function stopCamera() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                html5QrcodeScanner = null; // Important: Clear instance
                document.getElementById('reader').innerHTML = ''; // Force clear DOM
                document.getElementById('startScanBtn').style.display = 'inline-block';
                document.getElementById('stopScanBtn').style.display = 'none';
            }).catch(err => console.error(err));
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;
        
        beep.play().catch(e => {});
        handleScan(decodedText);
        
        // Optional: Pause scanning slightly to prevent double scan?
        // But users want fast scanning. We handle duplicates in logic.
    }

    function onScanFailure(error) {
        // console.warn(`Scan error = ${error}`);
    }

    function handleScan(code) {
        code = code.trim();
        if (!code) return;

        isProcessing = true;
        errorAlert.style.display = 'none';

        // Check search endpoint
        fetch('{{ route("scanner.search") }}', {
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
                addToCart(data.item);
                lastScannedInfo.style.display = 'block';
                lastScannedName.textContent = data.item.name;
            } else {
                showError(data.message);
                // Play error sound?
            }
        })
        .catch(err => {
            showError("Terjadi kesalahan jaringan");
        })
        .finally(() => {
            isProcessing = false;
        });
    }

    function addToCart(item) {
        const existingIndex = cart.findIndex(c => c.id === item.id);
        
        if (existingIndex > -1) {
            cart[existingIndex].qty += 1;
        } else {
            cart.push({
                ...item,
                qty: 1
            });
        }
        
        renderCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function updateCartQty(index, newQty) {
        newQty = parseInt(newQty);
        if (newQty < 1) newQty = 1;
        
        cart[index].qty = newQty;
        renderCart();
    }

    function renderCart() {
        cartCount.textContent = cart.length + ' Item';
        cartTableBody.innerHTML = '';

        if (cart.length === 0) {
            cartTableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">
                        <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                        Belum ada barang discan
                    </td>
                </tr>
            `;
            processBtn.disabled = true;
            return;
        }

        processBtn.disabled = false;
        const currentMode = document.querySelector('input[name="scanMode"]:checked').value;

        cart.forEach((item, index) => {
            // Check stock for OUT mode
            let stockWarning = '';
            if (currentMode === 'out' && item.qty > item.stock) {
                stockWarning = `<div class="text-danger small"><i class="bi bi-exclamation-circle"></i> Stok kurang! (Sisa: ${item.stock})</div>`;
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="fw-bold">${item.name}</div>
                    <div class="small text-muted">${item.code}</div>
                    ${stockWarning}
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <button class="btn btn-outline-secondary btn-minus" type="button">-</button>
                        <input type="number" class="form-control text-center qty-input" value="${item.qty}" min="1">
                        <button class="btn btn-outline-secondary btn-plus" type="button">+</button>
                    </div>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-danger btn-remove">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            // Event Listeners for row actions
            tr.querySelector('.btn-minus').addEventListener('click', () => updateCartQty(index, item.qty - 1));
            tr.querySelector('.btn-plus').addEventListener('click', () => updateCartQty(index, item.qty + 1));
            tr.querySelector('.qty-input').addEventListener('change', (e) => updateCartQty(index, e.target.value));
            tr.querySelector('.btn-remove').addEventListener('click', () => removeFromCart(index));

            cartTableBody.appendChild(tr);
        });

        validateCartStock();
    }

    function validateCartStock() {
        const mode = document.querySelector('input[name="scanMode"]:checked').value;
        const processBtn = document.getElementById('processBtn');
        let isValid = true;

        if (cart.length === 0) {
            isValid = false;
        } else if (mode === 'out') {
            // Check if any item exceeds stock
            const hasExceededStock = cart.some(item => item.qty > item.stock);
            if (hasExceededStock) isValid = false;
        }

        processBtn.disabled = !isValid;
        
        // Re-render requested? No, usually called BY renderCart.
        // But if called by Mode Switch, we need to update UI warnings
        if (event && event.type === 'change') {
             // Re-render to show/hide warnings based on new mode
             // Avoid infinite loop if renderCart calls validateCartStock
             // Just triggering a re-render is safer logic-wise
             // renderCart(); // But renderCart calls us.
             // So we actually need to separate rendering logic from validation?
             // Since renderCart renders warnings based on mode, calling renderCart() is enough.
             // We can skip calling renderCart() inside validateCartStock() and assume caller handles it.
             // But let's just Refresh the table when mode changes.
             cartTableBody.innerHTML = ''; 
             const tempCart = [...cart]; // trigger re-render
             // Actually, simplest is direct re-render
             // BUT we can't call renderCart inside renderCart loop.
             // So: validateCartStock just updates button enabled state.
             // Mode change listener calls renderCart().
        }
    }

    // Override mode change listener to re-render
    modeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateModeUI();
            renderCart(); // Re-render to update warnings
        });
    });

    function showError(msg) {
        errorMessage.textContent = msg;
        errorAlert.style.display = 'block';
        // Auto hide
        setTimeout(() => {
            errorAlert.style.display = 'none';
        }, 3000);
    }

    function submitTransaction() {
        if (!confirm('Apakah Anda yakin ingin memproses transaksi ini?')) return;

        const form = document.getElementById('transactionForm');
        const itemsContainer = document.getElementById('formItemsContainer');
        const mode = document.querySelector('input[name="scanMode"]:checked').value;
        const notes = document.getElementById('globalNotes').value;

        document.getElementById('formType').value = mode;
        document.getElementById('formNotes').value = notes;
        itemsContainer.innerHTML = '';

        cart.forEach((item, index) => {
            itemsContainer.innerHTML += `
                <input type="hidden" name="items[${index}][item_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
            `;
        });

        form.submit();
    }
    
    // Initial validation
    updateModeUI();
});
</script>
@endpush
