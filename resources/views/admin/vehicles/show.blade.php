@extends('layouts.app')

@section('title', 'Detail Kendaraan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Detail Kendaraan</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}">Vehicles</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if(can('vehicles.edit'))
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-primary"><i class="ti ti-edit me-1"></i> Edit</a>
            @endif
            <a href="{{ route('vehicles.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card card-lg">
                <div class="card-body text-center">
                    <img src="{{ $vehicle->photo ? Storage::disk('minio')->url($vehicle->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($vehicle->vehicle_code) . '&background=0d6efd&color=fff&size=128' }}" alt="" class="rounded-circle object-fit-cover border mb-3" width="120" height="120">
                    <h5 class="mb-1">{{ $vehicle->vehicle_code }}</h5>
                    <div class="text-muted">{{ $vehicle->plate_number }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card card-lg">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6"><strong>Kode Kendaraan:</strong><div>{{ $vehicle->vehicle_code }}</div></div>
                        <div class="col-md-6"><strong>Plat Nomor:</strong><div>{{ $vehicle->plate_number }}</div></div>
                        <div class="col-md-6"><strong>Jenis:</strong><div>{{ $vehicle->vehicle_type }}</div></div>
                        <div class="col-md-6"><strong>Status:</strong><div><span class="badge bg-success-subtle text-success-emphasis">{{ ucfirst($vehicle->status) }}</span></div></div>
                        <div class="col-md-6"><strong>Brand:</strong><div>{{ $vehicle->brand ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>Model:</strong><div>{{ $vehicle->model ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>Warna:</strong><div>{{ $vehicle->color ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>Tahun:</strong><div>{{ $vehicle->year ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>Kapasitas:</strong><div>{{ $vehicle->capacity ? $vehicle->capacity . ' orang' : '-' }}</div></div>
                        <div class="col-md-6"><strong>Pemilik:</strong><div>{{ $vehicle->owner_name ?? '-' }}</div></div>
                        <div class="col-12"><strong>Dibuat:</strong><div>{{ $vehicle->created_at ? tgl_indo($vehicle->created_at) : '-' }}</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
