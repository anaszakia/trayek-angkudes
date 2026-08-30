@extends('layouts.app')

@section('title', 'Jadwal Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Jadwal Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Jadwal</li>
                </ol>
            </nav>
        </div>
        @if(can('schedules.create'))
            <a href="{{ route('schedules.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Tambah Jadwal</a>
        @endif
    </div>

    <div class="card card-lg">
        <div class="card-body p-0">
            <div class="p-4 border-bottom">
                <form action="{{ route('schedules.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari kode jadwal atau trayek">
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('schedules.index') }}" class="btn btn-white">Reset</a>
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
                            <th>Kode Jadwal</th>
                            <th>Trayek</th>
                            <th>Hari</th>
                            <th>Berangkat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schedules as $schedule)
                            <tr>
                                <td>{{ $schedules->firstItem() + $loop->index }}</td>
                                <td>{{ $schedule->schedule_code }}</td>
                                <td>{{ $schedule->route?->name ?? '-' }}</td>
                                <td>{{ $schedule->day_of_week }}</td>
                                <td>{{ $schedule->departure_time }}</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'active' => 'bg-success-subtle text-success-emphasis',
                                            'inactive' => 'bg-secondary-subtle text-secondary-emphasis',
                                        ][$schedule->status] ?? 'bg-light text-dark';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($schedule->status) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if(can('schedules.view'))
                                            <a href="{{ route('schedules.show', $schedule) }}" class="btn btn-sm btn-white" title="Detail"><i class="ti ti-eye"></i></a>
                                        @endif
                                        @if(can('schedules.edit'))
                                            <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-sm btn-white" title="Edit"><i class="ti ti-edit"></i></a>
                                        @endif
                                        @if(can('schedules.delete'))
                                            <form action="{{ route('schedules.destroy', $schedule) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-white text-danger" data-confirm="Yakin hapus jadwal {{ $schedule->schedule_code }}?" title="Hapus"><i class="ti ti-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-6">
                                    @if(request('search'))
                                        Jadwal tidak ditemukan.
                                    @else
                                        Belum ada jadwal.
                                        <a href="{{ route('schedules.create') }}">Tambah sekarang</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($schedules->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $schedules->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
