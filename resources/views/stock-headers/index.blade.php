@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Daftar Transaksi</h4>
        <p class="text-muted mb-0">Dokumen transaksi stok masuk dan keluar</p>
    </div>
    @if(auth()->user()->canCreateTransaction())
    <div class="btn-group">
        <a href="{{ route('stock-headers.create-in') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i> Stok Masuk
        </a>
        <a href="{{ route('stock-headers.create-out') }}" class="btn btn-danger">
            <i class="bi bi-dash-lg me-1"></i> Stok Keluar
        </a>
    </div>
    @endif
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form id="filterForm" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Gudang</label>
                @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                    <select name="warehouse_id" class="form-select filter-input">
                        <option value="">Semua Gudang</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->warehouse->name ?? '-' }}" readonly>
                    <input type="hidden" name="warehouse_id" value="{{ auth()->user()->warehouse_id }}">
                @endif
            </div>
            <div class="col-md-3">
                <label class="form-label small">Jenis Transaksi</label>
                <select name="type" class="form-select filter-input">
                    <option value="">Semua</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Masuk</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Keluar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control filter-input" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control filter-input" value="{{ request('end_date') }}">
            </div>
        </form>
    </div>
</div>

<!-- Transactions Table -->
<div class="card">
    <div class="card-body p-0" id="table-container">
        @include('stock-headers.partials.table')
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const tableContainer = document.getElementById('table-container');
    const inputs = filterForm.querySelectorAll('.filter-input');

    function fetchTable(url) {
        // Show loading state if needed
        tableContainer.style.opacity = '0.5';
        
        // If no url provided, build from form
        if (!url) {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            url = '{{ route("stock-headers.index") }}?' + params.toString();
        }

        // Update URL
        history.pushState(null, '', url);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
            tableContainer.style.opacity = '1';
            rebindPagination();
        })
        .catch(error => {
            console.error('Error:', error);
            tableContainer.style.opacity = '1';
        });
    }

    function rebindPagination() {
        const links = tableContainer.querySelectorAll('.pagination a');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchTable(this.href);
            });
        });
    }

    // Attach listeners
    inputs.forEach(input => {
        input.addEventListener('change', () => fetchTable());
        // For date inputs, change event is good enough
    });

    // Initial binding
    rebindPagination();
});
</script>
@endpush
