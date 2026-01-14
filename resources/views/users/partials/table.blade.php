<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Gudang</th>
                <th>Dibuat</th>
                <th style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td class="fw-semibold">
                    {{ $user->name }}
                    @if($user->id === auth()->id())
                    <span class="badge bg-info ms-1">Anda</span>
                    @endif
                </td>
                <td>{{ $user->email }}</td>
                <td>
                    @php
                        $badgeClass = match($user->role) {
                            'admin' => 'bg-danger',
                            'owner' => 'bg-warning text-dark',
                            'staff' => 'bg-primary',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ \App\Models\User::getRoles()[$user->role] ?? $user->role }}</span>
                </td>
                <td>{{ $user->warehouse->name ?? '-' }}</td>
                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('users.edit', $user) }}">
                                    <i class="bi bi-pencil text-primary me-2"></i> Edit
                                </a>
                            </li>
                            @if($user->id !== auth()->id())
                            <li>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
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
                <td colspan="5" class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada user</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($users->hasPages())
<div class="card-footer">
    {{ $users->withQueryString()->links() }}
</div>
@endif
