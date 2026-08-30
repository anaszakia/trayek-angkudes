@extends('layouts.app')

@section('title', 'Detail Jadwal')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Detail Jadwal</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('schedules.index') }}">Jadwal</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if(can('schedules.edit'))
                <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-primary"><i class="ti ti-edit me-1"></i> Edit</a>
            @endif
            <a href="{{ route('schedules.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6"><strong>Kode Jadwal:</strong><div>{{ $schedule->schedule_code }}</div></div>
                <div class="col-md-6"><strong>Trayek:</strong><div>{{ $schedule->route?->name ?? '-' }}</div></div>
                <div class="col-md-4"><strong>Hari:</strong><div>{{ $schedule->day_of_week }}</div></div>
                <div class="col-md-4"><strong>Jam Berangkat:</strong><div>{{ $schedule->departure_time }}</div></div>
                <div class="col-md-4"><strong>Jam Tiba:</strong><div>{{ $schedule->arrival_time }}</div></div>
                <div class="col-md-4"><strong>Frekuensi:</strong><div>{{ $schedule->frequency_minutes ? $schedule->frequency_minutes . ' menit' : '-' }}</div></div>
                <div class="col-md-4"><strong>Status:</strong><div><span class="badge bg-success-subtle text-success-emphasis">{{ ucfirst($schedule->status) }}</span></div></div>
                <div class="col-12"><strong>Deskripsi:</strong><div>{{ $schedule->description ?? '-' }}</div></div>
            </div>
        </div>
    </div>
@endsection
