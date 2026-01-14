@extends('layouts.app')

@section('title', 'Laporan Harian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Laporan Harian</h4>
        <p class="text-muted mb-0">Transaksi tanggal: {{ $date->format('d F Y') }}</p>
    </div>
    <div>
        <a href="{{ route('reports.daily.pdf', ['date' => $date->toDateString(), 'warehouse_id' => request('warehouse_id')]) }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
        <a href="{{ route('reports.daily.excel', ['date' => $date->toDateString(), 'warehouse_id' => request('warehouse_id')]) }}" class="btn btn-success ms-2">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
    </div>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form id="filterForm" class="row g-3 align-items-end">
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
                <label class="form-label">Pilih Tanggal</label>
                <input type="date" name="date" class="form-control filter-input" value="{{ $date->toDateString() }}">
            </div>
        </form>
    </div>
</div>

<div id="report-content">
    @include('reports.partials.daily_content')
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const reportContent = document.getElementById('report-content');
    const inputs = filterForm.querySelectorAll('.filter-input');
    const pdfBtn = document.querySelector('.btn-danger');
    const excelBtn = document.querySelector('.btn-success.ms-2');

    function fetchReport(url) {
        // Show loading state
        reportContent.style.opacity = '0.5';
        
        // If no url provided, build from form
        if (!url) {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            url = '{{ route("reports.daily") }}?' + params.toString();
            
            // Update Export Links
            const pdfUrl = new URL(pdfBtn.href);
            pdfUrl.search = params.toString();
            pdfBtn.href = pdfUrl.toString();

            const excelUrl = new URL(excelBtn.href);
            excelUrl.search = params.toString();
            excelBtn.href = excelUrl.toString();
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
