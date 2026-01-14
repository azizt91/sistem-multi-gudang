<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stats-card primary">
            <div class="stats-value">{{ number_format($summary['total_items']) }}</div>
            <div class="stats-label">Total Jenis Barang</div>
            <i class="bi bi-box-seam stats-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card info">
            <div class="stats-value">{{ number_format($summary['total_stock']) }}</div>
            <div class="stats-label">Total Stok</div>
            <i class="bi bi-boxes stats-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card warning">
            <div class="stats-value">{{ number_format($summary['low_stock_count']) }}</div>
            <div class="stats-label">Stok Menipis</div>
            <i class="bi bi-exclamation-triangle stats-icon"></i>
        </div>
    </div>
</div>

<!-- Stock Table -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul me-2"></i>Daftar Stok Barang
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="150" class="bg-light">Gudang</th> <!-- Added Warehouse Column -->
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Min</th>
                        <th>Satuan</th>
                        <th>Status</th>
                        <th>Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->warehouse_name }}</td> <!-- Show Warehouse Name -->
                        <td><code>{{ $item->item_code }}</code></td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->category_name }}</td>
                        <td class="text-center fw-bold {{ $item->status == 'Low Stock' ? 'text-danger' : 'text-success' }}">
                            {{ $item->stock }}
                        </td>
                        <td class="text-center">{{ $item->minimum_stock }}</td>
                        <td>{{ $item->unit_name }}</td>
                        <td>
                            @if($item->status == 'Low Stock')
                            <span class="badge bg-danger">Stok Menipis</span>
                            @else
                            <span class="badge bg-success">Normal</span>
                            @endif
                        </td>
                        <td>{{ $item->rack_location ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada barang</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
