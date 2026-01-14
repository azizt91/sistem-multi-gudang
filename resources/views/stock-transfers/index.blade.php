@extends('layouts.app')

@section('title', 'Transfer Stok')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Transfer Stok Antar Gudang</h4>
        <p class="text-muted mb-0">Kelola perpindahan stok antar gudang</p>
    </div>
    @if(!auth()->user()->isOwner())
    <a href="{{ route('stock-transfers.create') }}" class="btn btn-primary">
        <i class="bi bi-arrow-left-right me-1"></i> Transfer Baru
    </a>
    @endif
</div>

@if(auth()->user()->isAdmin() || auth()->user()->isOwner())
<div class="card mb-4">
    <div class="card-body">
        <form id="filterForm" class="row g-3">
            <div class="col-md-4">
                <select name="warehouse_id" class="form-select filter-input">
                    <option value="">Semua Gudang</option>
                    @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                        {{ $warehouse->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body p-0" id="table-container">
        @include('stock-transfers.partials.table')
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const tableContainer = document.getElementById('table-container');

    // If filter form doesn't exist (e.g. staff), no JS needed for filtering
    if (!filterForm) return;

    const inputs = filterForm.querySelectorAll('.filter-input');

    function fetchTable(url) {
        // Show loading state
        tableContainer.style.opacity = '0.5';
        
        // If no url provided, build from form
        if (!url) {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            url = '{{ route("stock-transfers.index") }}?' + params.toString();
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
    });

    // Initial binding for pagination
    rebindPagination();
});
</script>
@endpush
