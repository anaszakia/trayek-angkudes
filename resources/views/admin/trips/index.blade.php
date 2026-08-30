@extends('layouts.app')

@section('title', 'Trip Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Trip Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Trip</li>
                </ol>
            </nav>
        </div>
        @if(can('trips.start'))
            <a href="{{ route('trips.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Tambah Trip</a>
        @endif
    </div>

    <div class="card card-lg">
        <div class="card-body p-0">
            <div class="p-4 border-bottom">
                <form action="{{ route('trips.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari kode trip atau status">
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('trips.index') }}" class="btn btn-white">Reset</a>
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
                            <th>Kode Trip</th>
                            <th>Trayek</th>
                            <th>Driver</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trips as $trip)
                            <tr>
                                <td>{{ $trips->firstItem() + $loop->index }}</td>
                                <td>{{ $trip->trip_code }}</td>
                                <td>{{ $trip->route?->name ?? '-' }}</td>
                                <td>{{ $trip->driver?->user?->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'scheduled' => 'bg-secondary-subtle text-secondary-emphasis',
                                            'in_progress' => 'bg-primary-subtle text-primary-emphasis',
                                            'completed' => 'bg-success-subtle text-success-emphasis',
                                            'cancelled' => 'bg-danger-subtle text-danger-emphasis',
                                        ][$trip->status] ?? 'bg-light text-dark';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $trip->status)) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if(can('trips.view'))
                                            <a href="{{ route('trips.show', $trip) }}" class="btn btn-sm btn-white" title="Detail"><i class="ti ti-eye"></i></a>
                                        @endif
                                        @if(can('trips.start'))
                                            <a href="{{ route('trips.edit', $trip) }}" class="btn btn-sm btn-white" title="Edit"><i class="ti ti-edit"></i></a>
                                        @endif
                                        @if(can('trips.history'))
                                            <form action="{{ route('trips.destroy', $trip) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-white text-danger" data-confirm="Yakin hapus trip {{ $trip->trip_code }}?" title="Hapus"><i class="ti ti-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-6">
                                    @if(request('search'))
                                        Trip tidak ditemukan.
                                    @else
                                        Belum ada trip.
                                        <a href="{{ route('trips.create') }}">Tambah sekarang</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($trips->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $trips->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
