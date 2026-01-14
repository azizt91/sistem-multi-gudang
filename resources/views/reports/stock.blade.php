@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Laporan Stok</h4>
        <p class="text-muted mb-0">Status stok semua barang</p>
    </div>
    <div>
        <a href="{{ route('reports.stock.excel', request()->query()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('reports.stock.pdf', request()->query()) }}" class="btn btn-danger ms-2">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form id="filterForm" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Gudang</label>
                <select name="warehouse_id" class="form-select filter-input">
                    <option value="">Semua Gudang</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select filter-input">
                    <option value="">Semua Kategori</option>
                    @foreach(\App\Models\Category::orderBy('name')->get() as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                 <div class="form-check form-switch mt-4">
                    <input class="form-check-input filter-input" type="checkbox" name="low_stock" value="1" id="lowStock" {{ request('low_stock') ? 'checked' : '' }}>
                    <label class="form-check-label" for="lowStock">Hanya Stok Menipis</label>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                 <a href="{{ route('reports.stock') }}" class="btn btn-secondary w-100">Reset Filter</a>
            </div>
        </form>
    </div>
</div>

<div id="report-content">
    @include('reports.partials.stock_content')
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const reportContent = document.getElementById('report-content');
    const inputs = filterForm.querySelectorAll('.filter-input');
    const excelBtn = document.querySelector('.btn-success');
    const pdfBtn = document.querySelector('.btn-danger');

    function fetchReport(url) {
        // Show loading state
        reportContent.style.opacity = '0.5';
        
        // If no url provided, build from form
        if (!url) {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            url = '{{ route("reports.stock") }}?' + params.toString();
            
            // Update Export Links
            const excelUrl = new URL(excelBtn.href);
            excelUrl.search = params.toString();
            excelBtn.href = excelUrl.toString();

            const pdfUrl = new URL(pdfBtn.href);
            pdfUrl.search = params.toString();
            pdfBtn.href = pdfUrl.toString();
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
            reportContent.innerHTML = html;
            reportContent.style.opacity = '1';
        })
        .catch(error => {
            console.error('Error:', error);
            reportContent.style.opacity = '1';
        });
    }

    // Attach listeners
    inputs.forEach(input => {
        input.addEventListener('change', () => fetchReport());
    });
});
</script>
@endpush
