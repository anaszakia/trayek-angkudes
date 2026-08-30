@extends('layouts.app')

@section('title', 'Driver Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Driver Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Drivers</li>
                </ol>
            </nav>
        </div>
        @if(can('drivers.create'))
            <a href="{{ route('drivers.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Tambah Driver
            </a>
        @endif
    </div>

    <div class="card card-lg">
        <div class="card-body p-0">
            <div class="p-4 border-bottom">
                <form action="{{ route('drivers.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari driver, kode, NIK, nomor SIM">
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('drivers.index') }}" class="btn btn-white">Reset</a>
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
                            <th>Kode</th>
                            <th>SIM</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($drivers as $driver)
                            <tr>
                                <td>{{ $drivers->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $driver->photo ? Storage::disk('minio')->url($driver->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($driver->user?->name ?? $driver->driver_code) . '&background=0d6efd&color=fff&size=128' }}"
                                            alt="{{ $driver->user?->name ?? $driver->driver_code }}"
                                            class="rounded-circle object-fit-cover"
                                            width="38" height="38" />
                                        <div>
                                            <div class="fw-semibold">{{ $driver->user?->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $driver->phone ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $driver->driver_code }}</td>
                                <td>{{ $driver->license_number ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'active' => 'bg-success-subtle text-success-emphasis',
                                            'inactive' => 'bg-secondary-subtle text-secondary-emphasis',
                                            'suspended' => 'bg-danger-subtle text-danger-emphasis',
                                        ][$driver->status] ?? 'bg-light text-dark';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($driver->status) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if(can('drivers.view'))
                                            <a href="{{ route('drivers.show', $driver) }}" class="btn btn-sm btn-white" title="Detail"><i class="ti ti-eye"></i></a>
                                        @endif
                                        @if(can('drivers.edit'))
                                            <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-sm btn-white" title="Edit"><i class="ti ti-edit"></i></a>
                                        @endif
                                        @if(can('drivers.delete'))
                                            <form action="{{ route('drivers.destroy', $driver) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-white text-danger" data-confirm="Yakin hapus driver {{ $driver->driver_code }}?" title="Hapus"><i class="ti ti-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-6">
                                    @if(request('search'))
                                        Driver tidak ditemukan.
                                    @else
                                        Belum ada driver.
                                        <a href="{{ route('drivers.create') }}">Tambah sekarang</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($drivers->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $drivers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
