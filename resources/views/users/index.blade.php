@extends('layouts.app')

@section('title', 'Daftar User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Daftar User</h4>
        <p class="text-muted mb-0">Kelola pengguna sistem</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah User
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form id="filterForm" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control filter-input" name="search" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="role" class="form-select filter-input">
                    <option value="">Semua Role</option>
                    @foreach(\App\Models\User::getRoles() as $key => $value)
                    <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0" id="table-container">
        @include('users.partials.table')
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const tableContainer = document.getElementById('table-container');
    const inputs = filterForm.querySelectorAll('.filter-input');
    let debounceTimer;

    function fetchTable(url) {
        // Show loading state
        tableContainer.style.opacity = '0.5';
        
        // If no url provided, build from form
        if (!url) {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            url = '{{ route("users.index") }}?' + params.toString();
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
        if (input.tagName === 'SELECT') {
            input.addEventListener('change', () => fetchTable());
        } else {
            // Debounce for text inputs
            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => fetchTable(), 500);
            });
        }
    });

    // Initial binding
    rebindPagination();
});
</script>
@endpush
