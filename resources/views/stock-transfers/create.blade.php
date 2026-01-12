@extends('layouts.app')

@section('title', 'Transfer Stok Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
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
                                <select class="form-select bg-light" disabled>
                                    @foreach($warehouses as $w)
                                    <option value="{{ $w->id }}" {{ auth()->user()->warehouse_id == $w->id ? 'selected' : '' }}>
                                        {{ $w->name }} (Gudang Anda)
                                    </option>
                                    @endforeach
                                </select>
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

                    <!-- Items Table -->
                    <label class="form-label fw-bold mb-3">Daftar Barang yang Ditransfer <span class="text-danger">*</span></label>
                    
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40%">Nama Barang</th>
                                    <th style="width: 20%">Jumlah</th>
                                    <th style="width: 30%">Catatan Item</th>
                                    <th style="width: 10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemsContainer">
                                <tr class="item-row">
                                    <td>
                                        <select class="form-select item-select" name="items[0][item_id]" required>
                                            <option value="">Pilih Barang</option>
                                            @foreach($items as $item)
                                            <option value="{{ $item->id }}" data-unit="{{ $item->unit->abbreviation }}">
                                                {{ $item->code }} - {{ $item->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="items[0][quantity]" min="1" required>
                                            <span class="input-group-text unit-label">-</span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="items[0][notes]" placeholder="Opsional">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-row" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-success btn-sm mb-4" id="addRow">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Baris
                    </button>

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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowCount = 1;
        const container = document.getElementById('itemsContainer');
        const btnAdd = document.getElementById('addRow');
        const form = document.getElementById('transferForm');
        
        // Warehouse Validation
        const sourceSelect = document.getElementById('source_warehouse_id');
        const destSelect = document.getElementById('destination_warehouse_id');
        
        function validateWarehouses() {
            const errorDiv = document.getElementById('warehouse-error');
            if (sourceSelect.value && destSelect.value && sourceSelect.value === destSelect.value) {
                destSelect.classList.add('is-invalid');
                errorDiv.style.display = 'block';
                document.getElementById('submitBtn').disabled = true;
            } else {
                destSelect.classList.remove('is-invalid');
                errorDiv.style.display = 'none';
                document.getElementById('submitBtn').disabled = false;
            }
        }

        sourceSelect.addEventListener('change', validateWarehouses);
        destSelect.addEventListener('change', validateWarehouses);

        // Add Row
        btnAdd.addEventListener('click', function() {
            const template = container.querySelector('.item-row').cloneNode(true);
            const inputs = template.querySelectorAll('input, select');
            
            inputs.forEach(input => {
                input.value = '';
                // Update name index
                const name = input.getAttribute('name');
                if(name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${rowCount}]`));
                }
            });

            // Reset unit label
            template.querySelector('.unit-label').textContent = '-';
            
            // Enable remove button
            template.querySelector('.remove-row').disabled = false;

            container.appendChild(template);
            rowCount++;
        });

        // Remove Row
        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                if (container.querySelectorAll('.item-row').length > 1) {
                    e.target.closest('.item-row').remove();
                }
            }
        });

        // Update Unit on Item Select
        container.addEventListener('change', function(e) {
            if (e.target.classList.contains('item-select')) {
                const option = e.target.options[e.target.selectedIndex];
                const unit = option.dataset.unit || '-';
                const row = e.target.closest('tr');
                row.querySelector('.unit-label').textContent = unit;
            }
        });
    });
</script>
@endpush
@endsection
