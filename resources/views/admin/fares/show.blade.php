@extends('layouts.app')

@section('title', 'Detail Tarif')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Detail Tarif</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('fares.index') }}">Tarif</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if(can('fares.edit'))
                <a href="{{ route('fares.edit', $fare) }}" class="btn btn-primary"><i class="ti ti-edit me-1"></i> Edit</a>
            @endif
            <a href="{{ route('fares.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6"><strong>Kode Tarif:</strong><div>{{ $fare->fare_code }}</div></div>
                <div class="col-md-6"><strong>Nama Tarif:</strong><div>{{ $fare->name }}</div></div>
                <div class="col-md-4"><strong>Trayek:</strong><div>{{ $fare->route?->name ?? '-' }}</div></div>
                <div class="col-md-4"><strong>Jenis Penumpang:</strong><div>{{ ucfirst($fare->passenger_type) }}</div></div>
                <div class="col-md-4"><strong>Jumlah:</strong><div>{{ number_format($fare->amount, 0, ',', '.') }} {{ $fare->currency ?? 'IDR' }}</div></div>
                <div class="col-md-4"><strong>Berlaku Dari:</strong><div>{{ $fare->effective_from?->format('d M Y') ?? '-' }}</div></div>
                <div class="col-md-4"><strong>Berlaku Sampai:</strong><div>{{ $fare->effective_to?->format('d M Y') ?? '-' }}</div></div>
                <div class="col-md-4"><strong>Status:</strong><div><span class="badge bg-success-subtle text-success-emphasis">{{ ucfirst($fare->status) }}</span></div></div>
                <div class="col-12"><strong>Deskripsi:</strong><div>{{ $fare->description ?? '-' }}</div></div>
            </div>
        </div>
    </div>
@endsection
