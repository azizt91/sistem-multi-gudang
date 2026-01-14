<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>No. Dokumen</th>
                <th>Gudang</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th class="text-center">Jumlah Item</th>
                <th class="text-center">Total Qty</th>
                <th>Petugas</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($headers as $header)
            <tr>
                <td>
                    <a href="{{ route('stock-headers.show', $header) }}" class="fw-semibold text-decoration-none">
                        {{ $header->document_number }}
                    </a>
                </td>
                <td>
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-building me-1"></i>{{ $header->warehouse->name ?? '-' }}
                    </span>
                </td>
                <td>{{ $header->transaction_date->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="badge {{ $header->type_badge_class }}">
                        {{ $header->type_label }}
                    </span>
                </td>
                <td class="text-center">{{ $header->total_items }}</td>
                <td class="text-center fw-semibold {{ $header->type === 'in' ? 'text-success' : 'text-danger' }}">
                    {{ $header->type === 'in' ? '+' : '-' }}{{ $header->total_quantity }}
                </td>
                <td>{{ $header->user->name }}</td>
                <td>
                    @if($header->isReceiptLocked())
                    <span class="badge bg-info"><i class="bi bi-lock me-1"></i>Dikunci</span>
                    @elseif($header->hasCompleteSignatures())
                    <span class="badge bg-success"><i class="bi bi-check me-1"></i>Ditandatangani</span>
                    @else
                    <span class="badge bg-secondary">Belum TTD</span>
                    @endif
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('stock-headers.show', $header) }}">
                                    <i class="bi bi-eye text-info me-2"></i> Lihat Detail
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('stock-headers.receipt', $header) }}">
                                    <i class="bi bi-file-earmark-text text-primary me-2"></i> Tanda Terima
                                </a>
                            </li>
                            @if($header->hasCompleteSignatures())
                            <li>
                                <a class="dropdown-item" href="{{ route('stock-headers.pdf', $header) }}" target="_blank">
                                    <i class="bi bi-file-pdf text-danger me-2"></i> Download PDF
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() && !$header->isReceiptLocked())
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('stock-headers.destroy', $header) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus transaksi ini? Stok akan dikembalikan.')">
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
                    <p class="text-muted mt-2 mb-0">Tidak ada transaksi ditemukan</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($headers->hasPages())
<div class="card-footer">
    {{ $headers->withQueryString()->links() }}
</div>
@endif
