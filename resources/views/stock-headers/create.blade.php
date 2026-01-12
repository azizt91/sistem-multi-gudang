@extends('layouts.app')

@section('title', $type === 'in' ? 'Stok Masuk Baru' : 'Stok Keluar Baru')

@push('styles')
<style>
    /* Searchable Select Styles */
    .searchable-select-container {
        position: relative;
    }
    .searchable-select-input {
        cursor: pointer;
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
            <div class="card-header {{ $type === 'in' ? 'bg-success' : 'bg-danger' }} text-white">
                <i class="bi {{ $type === 'in' ? 'bi-box-arrow-in-down' : 'bi-box-arrow-up' }} me-2"></i>
                {{ $type === 'in' ? 'Input Stok Masuk' : 'Input Stok Keluar' }}
            </div>
            <div class="card-body">
                <form action="{{ route('stock-headers.store') }}" method="POST" id="transactionForm">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div class="mb-4">
                        <label for="warehouse_id" class="form-label">Gudang <span class="text-danger">*</span></label>
                        
                        @if($warehouses->count() === 1)
                            {{-- Staff: Auto-select and hide dropdown --}}
                            <input type="hidden" name="warehouse_id" value="{{ $warehouses->first()->id }}">
                            <input type="text" class="form-control bg-light" value="{{ $warehouses->first()->name }} ({{ $warehouses->first()->city ?? 'Utama' }})" readonly>
                        @else
                            {{-- Admin: Show dropdown --}}
                            <select name="warehouse_id" id="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $w)
                                    <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>
                                        {{ $w->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <!-- Header Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label">Catatan Transaksi</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" 
                                  placeholder="Contoh: Pembelian dari supplier X / Pengiriman ke customer Y">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Items Section -->
                    <div class="mb-4 p-3 rounded border" style="background: var(--table-hover);">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-semibold mb-0">
                                <i class="bi bi-box-seam me-2"></i>Daftar Barang
                            </h6>
                            <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Barang
                            </button>
                        </div>

                        <div id="itemsContainer">
                            <!-- Item row template will be added here -->
                        </div>

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

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('stock-headers.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn {{ $type === 'in' ? 'btn-success' : 'btn-danger' }}">
                            <i class="bi bi-check-lg me-1"></i> Simpan Transaksi
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
                @if($type === 'out')
                <small class="text-muted stock-info">Maks: -</small>
                @endif
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

<!-- Items Data for JS -->
<script>
    const itemsData = [
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
    const isStockOut = {{ $type === 'out' ? 'true' : 'false' }};
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('itemsContainer');
    const template = document.getElementById('itemRowTemplate');
    const addBtn = document.getElementById('addItemBtn');
    const totalItemsEl = document.getElementById('totalItems');
    const totalQuantityEl = document.getElementById('totalQuantity');
    let itemIndex = 0;

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.searchable-select-container')) {
            document.querySelectorAll('.searchable-select-dropdown.show').forEach(d => d.classList.remove('show'));
        }
    });

    function initSearchableSelect(row, preSelectedId = null) {
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
                if (filterLower === '' || searchText.includes(filterLower)) {
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
                html = '<div class="searchable-select-empty">Tidak ada barang ditemukan</div>';
            }

            optionsContainer.innerHTML = html;

            // Attach click events to options
            optionsContainer.querySelectorAll('.searchable-select-option').forEach(option => {
                option.addEventListener('click', function() {
                    const id = parseInt(this.dataset.id);
                    const item = itemsData.find(i => i.id === id);
                    if (item) {
                        selectItem(item);
                    }
                });
            });
        }

        function selectItem(item) {
            selectedItem = item;
            hiddenInput.value = item.id;
            displayInput.value = item.code + ' - ' + item.name;
            unitLabel.textContent = item.unit;
            
            if (isStockOut && stockInfo) {
                stockInfo.textContent = 'Maks: ' + item.stock;
                quantityInput.max = item.stock;
            }

            dropdown.classList.remove('show');
            updateTotals();
        }

        // Open dropdown
        displayInput.addEventListener('click', function(e) {
            e.stopPropagation();
            // Close other dropdowns
            document.querySelectorAll('.searchable-select-dropdown.show').forEach(d => {
                if (d !== dropdown) d.classList.remove('show');
            });
            dropdown.classList.toggle('show');
            if (dropdown.classList.contains('show')) {
                searchInput.value = '';
                renderOptions();
                searchInput.focus();
            }
        });

        // Search
        searchInput.addEventListener('input', function() {
            renderOptions(this.value);
        });

        // Prevent dropdown close on search input click
        searchInput.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Initial render
        renderOptions();

        // Check if pre-selected
        if (preSelectedId) {
            const item = itemsData.find(i => i.id === preSelectedId);
            if (item) {
                selectItem(item);
            }
        }
    }

    function addItemRow(preSelectedId = null) {
        const html = template.innerHTML.replace(/__INDEX__/g, itemIndex);
        const div = document.createElement('div');
        div.innerHTML = html;
        const row = div.firstElementChild;
        container.appendChild(row);
        
        // Initialize searchable select
        initSearchableSelect(row, preSelectedId);

        // Attach events
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

    // Add first row on load (with pre-selected item if any)
    const preselectedItemId = {{ request('item_id') ?? 'null' }};
    addItemRow(preselectedItemId);
});
</script>
@endpush
