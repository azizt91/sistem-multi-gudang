@extends('layouts.app')

@section('title', 'Transfer Stok Baru')

@push('styles')
<style>
    /* Searchable Select Styles */
    .searchable-select-container {
        position: relative;
    }
    .searchable-select-input {
        cursor: pointer;
        background-color: #fff !important;
    }
    .searchable-select-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 0.375rem;
        max-height: 300px;
        overflow-y: auto;
        display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .searchable-select-dropdown.show {
        display: block;
    }
    .searchable-select-search {
        padding: 8px 12px;
        border-bottom: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        background: var(--bg-card);
    }
    .searchable-select-search input {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background: var(--bg-card);
        color: var(--text-primary);
    }
    .searchable-select-options {
        max-height: 250px;
        overflow-y: auto;
    }
    .searchable-select-option {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid var(--table-hover);
        color: var(--text-primary);
    }
    .searchable-select-option:hover,
    .searchable-select-option.highlighted {
        background: var(--table-hover);
    }
    .searchable-select-option.selected {
        background: var(--primary);
        color: white;
    }
    .searchable-select-option small {
        display: block;
        color: var(--text-secondary);
        font-size: 0.75rem;
    }
    .searchable-select-option.selected small {
        color: rgba(255,255,255,0.8);
    }
    .searchable-select-empty {
        padding: 12px;
        text-align: center;
        color: var(--text-secondary);
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-arrow-left-right me-2"></i>Transfer Stok Baru
            </div>
            <div class="card-body">
                <form action="{{ route('stock-transfers.store') }}" method="POST" id="transferForm">
                    @csrf

                    <!-- Warehouse Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gudang Asal (Sumber) <span class="text-danger">*</span></label>
                            
                            @if(auth()->user()->isStaff())
                                {{-- For Staff: Read-only and auto-selected --}}
                                <input type="hidden" name="source_warehouse_id" id="source_warehouse_id" value="{{ auth()->user()->warehouse_id }}">
                                <input type="text" class="form-control bg-light" value="{{ auth()->user()->warehouse->name ?? 'Gudang Anda' }}" readonly>
                            @else
                                {{-- For Admin: Selectable --}}
                                <select name="source_warehouse_id" id="source_warehouse_id" class="form-select" required>
                                    <option value="">Pilih Gudang Asal</option>
                                    @foreach($warehouses as $w)
                                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gudang Tujuan <span class="text-danger">*</span></label>
                            <select name="destination_warehouse_id" id="destination_warehouse_id" class="form-select" required>
                                <option value="">Pilih Gudang Tujuan</option>
                                @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                                @endforeach
                            </select>
                            <div id="warehouse-error" class="invalid-feedback" style="display:none">
                                Gudang tujuan tidak boleh sama dengan gudang asal.
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Transfer</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Restock barang gudang cabang"></textarea>
                    </div>

                    <hr>

                    <!-- Items Section -->
                    <div class="mb-4 p-3 rounded border" style="background: var(--table-hover);">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-semibold mb-0">
                                <i class="bi bi-box-seam me-2"></i>Daftar Barang yang Ditransfer <span class="text-danger">*</span>
                            </h6>
                            <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Barang
                            </button>
                        </div>

                        <div id="itemsContainer"></div>
                        
                        @error('items')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Summary -->
                     <div class="card mb-4" style="background: var(--table-hover);">
                        <div class="card-body py-2">
                            <div class="row text-center">
                                <div class="col-6">
                                    <small class="text-muted">Total Item</small>
                                    <h5 class="mb-0" id="totalItems">0</h5>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Total Quantity</small>
                                    <h5 class="mb-0" id="totalQuantity">0</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('stock-transfers.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-save me-1"></i> Simpan Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Item Row Template -->
<template id="itemRowTemplate">
    <div class="item-row mb-3 p-3 border rounded bg-white" data-index="__INDEX__" style="background: var(--bg-card) !important;">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label small">Pilih Barang <span class="text-danger">*</span></label>
                <div class="searchable-select-container">
                    <input type="hidden" class="item-id-input" name="items[__INDEX__][item_id]" required>
                    <input type="text" class="form-control searchable-select-input" placeholder="🔍 Ketik untuk mencari barang..." readonly>
                    <div class="searchable-select-dropdown">
                        <div class="searchable-select-search">
                            <input type="text" class="search-input" placeholder="Cari kode atau nama barang...">
                        </div>
                        <div class="searchable-select-options">
                            <!-- Options will be populated by JS -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Jumlah <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" class="form-control quantity-input" name="items[__INDEX__][quantity]" 
                           min="1" value="1" required>
                    <span class="input-group-text unit-label" style="min-width: 50px;">-</span>
                </div>
                 <small class="text-muted stock-info">Maks: -</small>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Catatan Item</label>
                <input type="text" class="form-control" name="items[__INDEX__][notes]" 
                       placeholder="Catatan opsional">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn" title="Hapus">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
    // Initial Items Data from Controller
    let itemsData = [
        @foreach($items as $item)
        {
            id: {{ $item->id }},
            code: "{{ $item->code }}",
            name: "{{ addslashes($item->name) }}",
            stock: {{ $item->stock }},
            unit: "{{ $item->unit->abbreviation }}"
        },
        @endforeach
    ];

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('itemsContainer');
        const template = document.getElementById('itemRowTemplate');
        const addBtn = document.getElementById('addItemBtn');
        const totalItemsEl = document.getElementById('totalItems');
        const totalQuantityEl = document.getElementById('totalQuantity');
        const sourceSelect = document.getElementById('source_warehouse_id');
        const destSelect = document.getElementById('destination_warehouse_id');
        let itemIndex = 0;

         // Warehouse Validation & Dynamic Item Loading
         async function handleSourceChange() {
            validateWarehouses();
            
            const warehouseId = sourceSelect.value;
            if (!warehouseId) return;

            // Only fetch if admin (Staff logic handled by controller initial load)
            // But if admin switches, we need to fetch.
            // Check if select is not readonly/disabled
            if (!sourceSelect.disabled && sourceSelect.tagName === 'SELECT') {
                // Fetch items for this warehouse
                try {
                    // Show loading or disable inputs?
                    const response = await fetch(`{{ route('items.list') }}?warehouse_id=${warehouseId}`);
                    const data = await response.json();
                    
                    // Update itemsData
                    itemsData = data;
                    
                    // Clear existing rows because selection might be invalid now
                    container.innerHTML = '';
                    itemIndex = 0;
                    addItemRow(); // Add one empty row
                    
                } catch (error) {
                    console.error('Error fetching items:', error);
                    alert('Gagal memuat daftar barang dari gudang ini.');
                }
            }
        }

        if (sourceSelect) {
            sourceSelect.addEventListener('change', handleSourceChange);
        }
        
        if (destSelect) {
            destSelect.addEventListener('change', validateWarehouses);
        }

        function validateWarehouses() {
            const errorDiv = document.getElementById('warehouse-error');
            const submitBtn = document.getElementById('submitBtn');
            
            if (sourceSelect.value && destSelect.value && sourceSelect.value === destSelect.value) {
                destSelect.classList.add('is-invalid');
                errorDiv.style.display = 'block';
                submitBtn.disabled = true;
            } else {
                destSelect.classList.remove('is-invalid');
                errorDiv.style.display = 'none';
                submitBtn.disabled = false;
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.searchable-select-container')) {
                document.querySelectorAll('.searchable-select-dropdown.show').forEach(d => d.classList.remove('show'));
            }
        });

        function initSearchableSelect(row) {
            const container = row.querySelector('.searchable-select-container');
            const hiddenInput = container.querySelector('.item-id-input');
            const displayInput = container.querySelector('.searchable-select-input');
            const dropdown = container.querySelector('.searchable-select-dropdown');
            const searchInput = container.querySelector('.search-input');
            const optionsContainer = container.querySelector('.searchable-select-options');
            const unitLabel = row.querySelector('.unit-label');
            const stockInfo = row.querySelector('.stock-info');
            const quantityInput = row.querySelector('.quantity-input');

            let selectedItem = null;

            function renderOptions(filter = '') {
                const filterLower = filter.toLowerCase();
                let html = '';
                let count = 0;

                itemsData.forEach(item => {
                    const searchText = (item.code + ' ' + item.name).toLowerCase();
                    // Filter by search AND ensure stock > 0 for transfer
                    if ((filterLower === '' || searchText.includes(filterLower)) && item.stock > 0) {
                        const isSelected = selectedItem && selectedItem.id === item.id;
                        html += `
                            <div class="searchable-select-option ${isSelected ? 'selected' : ''}" data-id="${item.id}">
                                <strong>${item.code}</strong> - ${item.name}
                                <small>Stok: ${item.stock} ${item.unit}</small>
                            </div>
                        `;
                        count++;
                    }
                });

                if (count === 0) {
                    html = '<div class="searchable-select-empty">Tidak ada barang tersedia (Stok > 0)</div>';
                }

                optionsContainer.innerHTML = html;

                // Attach click events
                optionsContainer.querySelectorAll('.searchable-select-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const id = parseInt(this.dataset.id);
                        const item = itemsData.find(i => i.id === id);
                        if (item) selectItem(item);
                    });
                });
            }

            function selectItem(item) {
                selectedItem = item;
                hiddenInput.value = item.id;
                displayInput.value = item.code + ' - ' + item.name;
                unitLabel.textContent = item.unit;
                
                stockInfo.textContent = 'Maks: ' + item.stock;
                quantityInput.max = item.stock;
                
                dropdown.classList.remove('show');
                updateTotals();
            }

            // Open dropdown
            displayInput.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                // Close others
                document.querySelectorAll('.searchable-select-dropdown.show').forEach(d => {
                    if (d !== dropdown) d.classList.remove('show');
                });
                
                // Only open if warehouse selected
                if (!sourceSelect.value) {
                    alert('Pilih Gudang Asal terlebih dahulu');
                    return;
                }

                dropdown.classList.toggle('show');
                if (dropdown.classList.contains('show')) {
                    searchInput.value = '';
                    renderOptions();
                    searchInput.focus();
                }
            });

            searchInput.addEventListener('input', function() {
                renderOptions(this.value);
            });

            searchInput.addEventListener('click', e => e.stopPropagation());
        }

        function addItemRow() {
            const html = template.innerHTML.replace(/__INDEX__/g, itemIndex);
            const div = document.createElement('div');
            div.innerHTML = html;
            const row = div.firstElementChild;
            container.appendChild(row);
            
            initSearchableSelect(row);

            const quantityInput = row.querySelector('.quantity-input');
            const removeBtn = row.querySelector('.remove-item-btn');

            quantityInput.addEventListener('input', updateTotals);

            removeBtn.addEventListener('click', function() {
                row.remove();
                updateTotals();
            });

            itemIndex++;
            updateTotals();
        }

        function updateTotals() {
            const rows = container.querySelectorAll('.item-row');
            let totalQty = 0;
            
            rows.forEach(row => {
                const qty = parseInt(row.querySelector('.quantity-input').value) || 0;
                totalQty += qty;
            });

            totalItemsEl.textContent = rows.length;
            totalQuantityEl.textContent = totalQty;
        }

        addBtn.addEventListener('click', () => addItemRow());

        // Initial Row
        addItemRow();
    });
</script>
@endpush
@endsection
