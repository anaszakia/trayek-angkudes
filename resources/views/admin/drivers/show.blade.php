@extends('layouts.app')

@section('title', 'Detail Driver')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Detail Driver</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('drivers.index') }}">Drivers</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if(can('drivers.edit'))
                <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-primary"><i class="ti ti-edit me-1"></i> Edit</a>
            @endif
            <a href="{{ route('drivers.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card card-lg">
                <div class="card-body text-center">
                    <img src="{{ $driver->photo ? Storage::disk('minio')->url($driver->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($driver->user?->name ?? $driver->driver_code) . '&background=0d6efd&color=fff&size=128' }}" alt="" class="rounded-circle object-fit-cover border mb-3" width="120" height="120">
                    <h5 class="mb-1">{{ $driver->user?->name ?? '-' }}</h5>
                    <div class="text-muted">{{ $driver->driver_code }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card card-lg">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6"><strong>User:</strong><div>{{ $driver->user?->name ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>Email:</strong><div>{{ $driver->user?->email ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>NIK:</strong><div>{{ $driver->nik ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>No. Telepon:</strong><div>{{ $driver->phone ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>Nomor SIM:</strong><div>{{ $driver->license_number ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>Jenis SIM:</strong><div>{{ $driver->license_type ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>Status:</strong><div><span class="badge bg-success-subtle text-success-emphasis">{{ ucfirst($driver->status) }}</span></div></div>
                        <div class="col-md-6"><strong>Dibuat:</strong><div>{{ $driver->created_at ? tgl_indo($driver->created_at) : '-' }}</div></div>
                        <div class="col-12"><strong>Alamat:</strong><div>{{ $driver->address ?? '-' }}</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
