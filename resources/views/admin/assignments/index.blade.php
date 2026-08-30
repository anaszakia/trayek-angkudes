@extends('layouts.app')

@section('title', 'Assignment Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Assignment Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Assignments</li>
                </ol>
            </nav>
        </div>
        @if(can('assignments.create'))
            <a href="{{ route('assignments.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Buat Assignment</a>
        @endif
    </div>

    <div class="card card-lg">
        <div class="card-body p-0">
            <div class="p-4 border-bottom">
                <form action="{{ route('assignments.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari driver atau kendaraan">
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('assignments.index') }}" class="btn btn-white">Reset</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Driver</th>
                            <th>Kendaraan</th>
                            <th>Mulai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assignments as $assignment)
                            <tr>
                                <td>{{ $assignments->firstItem() + $loop->index }}</td>
                                <td>{{ $assignment->driver?->user?->name ?? '-' }}</td>
                                <td>{{ $assignment->vehicle?->plate_number ?? '-' }}</td>
                                <td>{{ $assignment->started_at ? tgl_indo($assignment->started_at) : '-' }}</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'active' => 'bg-success-subtle text-success-emphasis',
                                            'ended' => 'bg-secondary-subtle text-secondary-emphasis',
                                            'cancelled' => 'bg-danger-subtle text-danger-emphasis',
                                        ][$assignment->status] ?? 'bg-light text-dark';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($assignment->status) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if(can('assignments.view'))
                                            <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-sm btn-white" title="Detail"><i class="ti ti-eye"></i></a>
                                        @endif
                                        @if(can('assignments.edit'))
                                            <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-sm btn-white" title="Edit"><i class="ti ti-edit"></i></a>
                                        @endif
                                        @if(can('assignments.delete'))
                                            <form action="{{ route('assignments.destroy', $assignment) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-white text-danger" data-confirm="Yakin hapus assignment ini?" title="Hapus"><i class="ti ti-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-6">
                                    @if(request('search'))
                                        Assignment tidak ditemukan.
                                    @else
                                        Belum ada assignment.
                                        <a href="{{ route('assignments.create') }}">Tambah sekarang</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assignments->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
