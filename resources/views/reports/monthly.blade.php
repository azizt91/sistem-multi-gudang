@extends('layouts.app')

@section('title', 'Laporan Bulanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Laporan Bulanan</h4>
        <p class="text-muted mb-0">Periode: {{ $startDate->format('F Y') }}</p>
    </div>
    <div>
        <a href="{{ route('reports.monthly.pdf', ['month' => $month, 'year' => $year, 'warehouse_id' => request('warehouse_id')]) }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
        <a href="{{ route('reports.monthly.excel', ['month' => $month, 'year' => $year, 'warehouse_id' => request('warehouse_id')]) }}" class="btn btn-success ms-2">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
    </div>
</div>

<!-- Month Filter -->
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
                <label class="form-label">Bulan</label>
                <select name="month" class="form-select filter-input">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <select name="year" class="form-select filter-input">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </form>
    </div>
</div>

<div id="report-content">
    @include('reports.partials.monthly_content')
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
            url = '{{ route("reports.monthly") }}?' + params.toString();
            
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
