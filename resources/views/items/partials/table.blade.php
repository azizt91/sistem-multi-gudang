<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox" title="Pilih Semua">
                        </th>
                        <th style="width: 100px;">Barcode</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Stok</th>
                        <th>Satuan</th>
                        <th>Lokasi</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input item-checkbox" value="{{ $item->id }}" data-code="{{ $item->code }}">
                        </td>
                        <td>
                            <img src="{{ $item->generateBarcode() }}" alt="Barcode" class="img-fluid" style="max-height: 30px;">
                        </td>
                        <td><code>{{ $item->code }}</code></td>
                        <td>
                            <a href="{{ route('items.show', ['item' => $item, 'warehouse_id' => request('warehouse_id')]) }}" class="text-decoration-none fw-semibold">
                                {{ $item->name }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $item->category->name }}</span>
                        </td>
                        <td class="text-center">
                            @php
                                // Detect context logic if necessary, currently $item->stock is total stock unless filtered?
                                // Item logic for stock display:
                                // If warehouse_id filter is present, stock should reflect that? 
                                // The Controller's query uses whereHas... but doesn't override $item->stock attribute automatically unless we calculate it.
                                // However, let's stick to the existing view logic which uses $item->stock. 
                                // Ideally, if filtered by warehouse, we should show warehouse stock.
                                // But for now, just copying existing logic.
                            @endphp
                            
                            @if($item->isLowStock())
                            <span class="badge bg-danger low-stock-badge" title="Stok dibawah minimum ({{ $item->minimum_stock }})">
                                {{ $item->stock }}
                            </span>
                            @else
                            <span class="badge bg-success">{{ $item->stock }}</span>
                            @endif
                        </td>
                        <td>{{ $item->unit->abbreviation }}</td>
                        <td>{{ $item->rack_location ?? '-' }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('items.show', ['item' => $item, 'warehouse_id' => request('warehouse_id')]) }}">
                                            <i class="bi bi-eye text-info me-2"></i> Lihat Detail
                                        </a>
                                    </li>
                                    @if(auth()->user()->isAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('items.edit', ['item' => $item, 'warehouse_id' => request('warehouse_id')]) }}">
                                            <i class="bi bi-pencil text-primary me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
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
                            <p class="text-muted mt-2 mb-0">Tidak ada barang ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($items->hasPages())
    <div class="card-footer">
        {{ $items->withQueryString()->links() }}
    </div>
    @endif
</div>
