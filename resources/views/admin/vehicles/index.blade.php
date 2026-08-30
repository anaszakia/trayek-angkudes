@extends('layouts.app')

@section('title', 'Vehicle Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Vehicle Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Vehicles</li>
                </ol>
            </nav>
        </div>
        @if(can('vehicles.create'))
            <a href="{{ route('vehicles.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Tambah Kendaraan</a>
        @endif
    </div>

    <div class="card card-lg">
        <div class="card-body p-0">
            <div class="p-4 border-bottom">
                <form action="{{ route('vehicles.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari kode, plat, brand, model">
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('vehicles.index') }}" class="btn btn-white">Reset</a>
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
                            <th>Kendaraan</th>
                            <th>Kode</th>
                            <th>Plat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vehicles as $vehicle)
                            <tr>
                                <td>{{ $vehicles->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $vehicle->photo ? Storage::disk('minio')->url($vehicle->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($vehicle->vehicle_code) . '&background=0d6efd&color=fff&size=128' }}" alt="{{ $vehicle->vehicle_code }}" class="rounded-circle object-fit-cover" width="38" height="38" />
                                        <div>
                                            <div class="fw-semibold">{{ $vehicle->brand ?? '-' }} {{ $vehicle->model ?? '' }}</div>
                                            <small class="text-muted">{{ $vehicle->vehicle_type }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $vehicle->vehicle_code }}</td>
                                <td>{{ $vehicle->plate_number }}</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'active' => 'bg-success-subtle text-success-emphasis',
                                            'inactive' => 'bg-secondary-subtle text-secondary-emphasis',
                                            'maintenance' => 'bg-warning-subtle text-warning-emphasis',
                                        ][$vehicle->status] ?? 'bg-light text-dark';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($vehicle->status) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if(can('vehicles.view'))
                                            <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-white" title="Detail"><i class="ti ti-eye"></i></a>
                                        @endif
                                        @if(can('vehicles.edit'))
                                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-white" title="Edit"><i class="ti ti-edit"></i></a>
                                        @endif
                                        @if(can('vehicles.delete'))
                                            <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-white text-danger" data-confirm="Yakin hapus kendaraan {{ $vehicle->vehicle_code }}?" title="Hapus"><i class="ti ti-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-6">
                                    @if(request('search'))
                                        Kendaraan tidak ditemukan.
                                    @else
                                        Belum ada kendaraan.
                                        <a href="{{ route('vehicles.create') }}">Tambah sekarang</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($vehicles->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $vehicles->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
