<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Nomor Transfer</th>
                <th>Gudang Asal</th>
                <th>Gudang Tujuan</th>
                <th>Status</th>
                <th>User</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transfers as $transfer)
            <tr>
                <td class="fw-semibold">{{ $transfer->transfer_number }}</td>
                <td>{{ $transfer->sourceWarehouse->name }}</td>
                <td>{{ $transfer->destinationWarehouse->name }}</td>
                <td>
                    <span class="badge bg-success">Selesai</span>
                </td>
                <td>{{ $transfer->user->name }}</td>
                <td>{{ $transfer->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('stock-transfers.show', $transfer) }}" class="btn btn-sm btn-info text-white">
                        <i class="bi bi-eye"></i> Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="bi bi-arrow-left-right fs-1 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada data transfer stok</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($transfers->hasPages())
<div class="card-footer">
    {{ $transfers->links() }}
</div>
@endif
