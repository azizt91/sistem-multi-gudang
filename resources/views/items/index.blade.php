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
        <form class="row g-3" id="filterForm">
            <div class="col-md-4">
                <div class="input-group">
                    <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
                    <input type="text" class="form-control" name="search" placeholder="Cari kode atau nama barang..." value="{{ request('search') }}">
                </div>
            </div>
            @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
            <div class="col-md-3">
                <select name="warehouse_id" class="form-select">
                    <option value="">Semua Gudang (Total Stok)</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
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
            <div class="col-md-2">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="lowStock" {{ request('low_stock') ? 'checked' : '' }}>
                    <label class="form-check-label" for="lowStock">Stok Menipis</label>
                </div>
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

<!-- Items Table Container -->
<div id="items-container">
    @include('items.partials.table')
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectAllCheckbox = document.getElementById('selectAllCheckbox');
    let itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActionsDropdown = document.getElementById('bulkActionsDropdown');
    const selectedCountEl = document.getElementById('selectedCount');
    const printSelectedBtn = document.getElementById('printSelectedBtn');
    const printAllBtn = document.getElementById('printAllBtn');
    const printForm = document.getElementById('printForm');
    const selectedItemsContainer = document.getElementById('selectedItemsContainer');
    const printAllInput = document.getElementById('printAllInput');
    const filterForm = document.getElementById('filterForm');
    const itemsContainer = document.getElementById('items-container');

    // Debounce function to limit request frequency
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Generic function to load URL via AJAX
    function loadUrl(url) {
        history.pushState(null, '', url);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            itemsContainer.innerHTML = html;
            rebindEvents();
            updateUI();
        })
        .catch(error => console.error('Error fetching items:', error));
    }

    // Function to fetch items via AJAX (from filter)
    function fetchItems() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        const url = '{{ route("items.index") }}?' + params.toString();
        loadUrl(url);
    }

    // Attach listeners to filter inputs
    const inputs = filterForm.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.name === 'search') {
            input.addEventListener('input', debounce(fetchItems, 500));
        } else {
            input.addEventListener('change', fetchItems);
        }
        
        if (input.tagName === 'INPUT') {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    fetchItems();
                }
            });
        }
    });

    // Rebind events for dynamic content
    function rebindEvents() {
        selectAllCheckbox = document.getElementById('selectAllCheckbox');
        itemCheckboxes = document.querySelectorAll('.item-checkbox');
        
        // Rebind Checkboxes
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(cb => cb.checked = this.checked);
                updateUI();
            });
        }

        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateUI);
        });

        // Rebind Pagination Links
        const paginationLinks = itemsContainer.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                loadUrl(this.href);
            });
        });
    }

    function updateUI() {
        // Find fresh checkboxes
        itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const checked = document.querySelectorAll('.item-checkbox:checked');
        const count = checked.length;
        
        if (selectedCountEl) selectedCountEl.textContent = count;
        if (bulkActionsDropdown) bulkActionsDropdown.style.display = count > 0 ? 'block' : 'none';
        
        if (count === 0) {
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
        } else if (count === itemCheckboxes.length && itemCheckboxes.length > 0) {
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            }
        } else {
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                if (count > 0) selectAllCheckbox.indeterminate = true;
            }
        }
    }

    // Initial binding
    rebindEvents();

    // Print selected logic
    if (printSelectedBtn) {
        printSelectedBtn.addEventListener('click', function() {
            const checked = document.querySelectorAll('.item-checkbox:checked');
            if (checked.length === 0) {
                alert('Pilih minimal satu barang');
                return;
            }

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
    }

    // Print all logic
    if (printAllBtn) {
        printAllBtn.addEventListener('click', function() {
            const formData = new FormData(filterForm);
            printForm.elements['filter_search'].value = formData.get('search');
            printForm.elements['filter_category_id'].value = formData.get('category_id');
            printForm.elements['filter_low_stock'].value = formData.get('low_stock');
            
            selectedItemsContainer.innerHTML = '';
            printAllInput.value = '1';
            printForm.submit();
        });
    }
});
</script>
@endpush
