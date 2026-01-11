@extends('layouts.app')

@section('title', 'Daftar Barang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Daftar Barang</h4>
        <p class="text-muted mb-0">Kelola semua barang di gudang</p>
    </div>
    <div class="d-flex gap-2">
        <!-- Bulk Actions Dropdown -->
        <div class="dropdown" id="bulkActionsDropdown" style="display: none;">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-printer me-1"></i> Cetak Barcode
            </button>
            <ul class="dropdown-menu">
                <li>
                    <button type="button" class="dropdown-item" id="printSelectedBtn">
                        <i class="bi bi-check2-square text-primary me-2"></i> 
                        Cetak Terpilih (<span id="selectedCount">0</span>)
                    </button>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button type="button" class="dropdown-item" id="printAllBtn">
                        <i class="bi bi-grid text-success me-2"></i> 
                        Cetak Semua ({{ $items->total() }} barang)
                    </button>
                </li>
            </ul>
        </div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('items.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Barang
        </a>
        @endif
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('items.index') }}" method="GET" class="row g-3" id="filterForm">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Cari kode atau nama barang..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="lowStock" {{ request('low_stock') ? 'checked' : '' }}>
                    <label class="form-check-label" for="lowStock">Hanya Stok Menipis</label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden form for bulk print -->
<form action="{{ route('items.print-barcodes') }}" method="POST" id="printForm" target="_blank">
    @csrf
    <input type="hidden" name="quantity" value="1">
    <input type="hidden" name="print_all" value="0" id="printAllInput">
    <!-- Filter values for print all -->
    <input type="hidden" name="filter_search" value="{{ request('search') }}">
    <input type="hidden" name="filter_category_id" value="{{ request('category_id') }}">
    <input type="hidden" name="filter_low_stock" value="{{ request('low_stock') }}">
    <div id="selectedItemsContainer"></div>
</form>

<!-- Items Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox" title="Pilih Semua">
                        </th>
                        <th style="width: 100px;">Barcode</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Stok</th>
                        <th>Satuan</th>
                        <th>Lokasi</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input item-checkbox" value="{{ $item->id }}" data-code="{{ $item->code }}">
                        </td>
                        <td>
                            <img src="{{ $item->generateBarcode() }}" alt="Barcode" class="img-fluid" style="max-height: 30px;">
                        </td>
                        <td><code>{{ $item->code }}</code></td>
                        <td>
                            <a href="{{ route('items.show', $item) }}" class="text-decoration-none fw-semibold">
                                {{ $item->name }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $item->category->name }}</span>
                        </td>
                        <td class="text-center">
                            @if($item->isLowStock())
                            <span class="badge bg-danger low-stock-badge" title="Stok dibawah minimum ({{ $item->minimum_stock }})">
                                {{ $item->stock }}
                            </span>
                            @else
                            <span class="badge bg-success">{{ $item->stock }}</span>
                            @endif
                        </td>
                        <td>{{ $item->unit->abbreviation }}</td>
                        <td>{{ $item->rack_location ?? '-' }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('items.show', $item) }}">
                                            <i class="bi bi-eye text-info me-2"></i> Lihat Detail
                                        </a>
                                    </li>
                                    @if(auth()->user()->isAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('items.edit', $item) }}">
                                            <i class="bi bi-pencil text-primary me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada barang ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($items->hasPages())
    <div class="card-footer">
        {{ $items->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActionsDropdown = document.getElementById('bulkActionsDropdown');
    const selectedCountEl = document.getElementById('selectedCount');
    const printSelectedBtn = document.getElementById('printSelectedBtn');
    const printAllBtn = document.getElementById('printAllBtn');
    const printForm = document.getElementById('printForm');
    const selectedItemsContainer = document.getElementById('selectedItemsContainer');
    const printAllInput = document.getElementById('printAllInput');

    function updateUI() {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        const count = checked.length;
        
        selectedCountEl.textContent = count;
        bulkActionsDropdown.style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox state
        if (count === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (count === itemCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Select all checkbox
    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(cb => cb.checked = this.checked);
        updateUI();
    });

    // Individual checkboxes
    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateUI);
    });

    // Print selected
    printSelectedBtn.addEventListener('click', function() {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        if (checked.length === 0) {
            alert('Pilih minimal satu barang');
            return;
        }

        // Clear and populate hidden inputs
        selectedItemsContainer.innerHTML = '';
        printAllInput.value = '0';
        
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'item_ids[]';
            input.value = cb.value;
            selectedItemsContainer.appendChild(input);
        });

        printForm.submit();
    });

    // Print all (based on current filter)
    printAllBtn.addEventListener('click', function() {
        selectedItemsContainer.innerHTML = '';
        printAllInput.value = '1';
        printForm.submit();
    });

    // Initial state
    updateUI();
});
</script>
@endpush
