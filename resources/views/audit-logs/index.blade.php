@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Audit Logs</h4>
        <p class="text-muted mb-0">Riwayat aktivitas penting sistem</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('audit-logs.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select">
                    <option value="">Semua User</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 me-2">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="width: 15%">
                            <small>{{ $log->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td style="width: 15%">
                            <span class="fw-bold">{{ $log->user->name }}</span>
                            <br>
                            <small class="text-muted">{{ $log->user->role }}</small>
                        </td>
                        <td style="width: 15%">
                            <span class="badge bg-secondary font-monospace">{{ $log->action }}</span>
                        </td>
                        <td>
                            {{ $log->description }}
                            @if($log->entity_type && $log->entity_id)
                            <div class="mt-1">
                                <small class="text-muted font-monospace">
                                    Entity: {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
                                </small>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="bi bi-journal-text fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada data log.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
