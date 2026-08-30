@extends('layouts.app')

@section('title', 'Detail Trip')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Detail Trip</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('trips.index') }}">Trip</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if(can('trips.start'))
                <a href="{{ route('trips.edit', $trip) }}" class="btn btn-primary"><i class="ti ti-edit me-1"></i> Edit</a>
            @endif
            <a href="{{ route('trips.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6"><strong>Kode Trip:</strong><div>{{ $trip->trip_code }}</div></div>
                <div class="col-md-6"><strong>Trayek:</strong><div>{{ $trip->route?->name ?? '-' }}</div></div>
                <div class="col-md-4"><strong>Driver:</strong><div>{{ $trip->driver?->user?->name ?? '-' }}</div></div>
                <div class="col-md-4"><strong>Kendaraan:</strong><div>{{ $trip->vehicle?->plate_number ?? '-' }}</div></div>
                <div class="col-md-4"><strong>Jadwal:</strong><div>{{ $trip->schedule?->schedule_code ?? '-' }}</div></div>
                <div class="col-md-4"><strong>Status:</strong><div><span class="badge bg-success-subtle text-success-emphasis">{{ ucfirst(str_replace('_', ' ', $trip->status)) }}</span></div></div>
                <div class="col-md-4"><strong>Mulai:</strong><div>{{ $trip->started_at?->format('d M Y H:i') ?? '-' }}</div></div>
                <div class="col-md-4"><strong>Selesai:</strong><div>{{ $trip->ended_at?->format('d M Y H:i') ?? '-' }}</div></div>
                <div class="col-md-4"><strong>Total Penumpang:</strong><div>{{ $trip->total_passengers ?? 0 }}</div></div>
                <div class="col-12"><strong>Catatan:</strong><div>{{ $trip->notes ?? '-' }}</div></div>
            </div>
        </div>
    </div>
@endsection
