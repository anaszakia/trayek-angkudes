@extends('layouts.app')

@section('title', 'Detail Assignment')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Detail Assignment</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('assignments.index') }}">Assignments</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if(can('assignments.edit'))
                <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-primary"><i class="ti ti-edit me-1"></i> Edit</a>
            @endif
            <a href="{{ route('assignments.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6"><strong>Driver:</strong><div>{{ $assignment->driver?->user?->name ?? '-' }}</div></div>
                <div class="col-md-6"><strong>Kendaraan:</strong><div>{{ $assignment->vehicle?->plate_number ?? '-' }} / {{ $assignment->vehicle?->vehicle_code ?? '-' }}</div></div>
                <div class="col-md-4"><strong>Mulai:</strong><div>{{ $assignment->started_at ? tgl_indo($assignment->started_at) : '-' }}</div></div>
                <div class="col-md-4"><strong>Selesai:</strong><div>{{ $assignment->ended_at ? tgl_indo($assignment->ended_at) : '-' }}</div></div>
                <div class="col-md-4"><strong>Status:</strong><div><span class="badge bg-success-subtle text-success-emphasis">{{ ucfirst($assignment->status) }}</span></div></div>
                <div class="col-md-6"><strong>Nomor SIM:</strong><div>{{ $assignment->driver?->license_number ?? '-' }}</div></div>
                <div class="col-md-6"><strong>Jenis Kendaraan:</strong><div>{{ $assignment->vehicle?->vehicle_type ?? '-' }}</div></div>
            </div>
        </div>
    </div>
@endsection
