@extends('layouts.app')

@section('title', 'Detail Trayek')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Detail Trayek</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('routes.index') }}">Trayek</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if(can('routes.edit'))
                <a href="{{ route('routes.edit', $route) }}" class="btn btn-primary"><i class="ti ti-edit me-1"></i> Edit</a>
            @endif
            <a href="{{ route('routes.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6"><strong>Kode Trayek:</strong><div>{{ $route->code }}</div></div>
                <div class="col-md-6"><strong>Nama Trayek:</strong><div>{{ $route->name }}</div></div>
                <div class="col-md-4"><strong>Titik Awal:</strong><div>{{ $route->start_point }}</div></div>
                <div class="col-md-4"><strong>Titik Akhir:</strong><div>{{ $route->end_point }}</div></div>
                <div class="col-md-4"><strong>Tipe:</strong><div>{{ ucfirst(str_replace('_', ' ', $route->route_type)) }}</div></div>
                <div class="col-md-4"><strong>Jarak:</strong><div>{{ $route->distance_km ? $route->distance_km . ' KM' : '-' }}</div></div>
                <div class="col-md-4"><strong>Status:</strong><div><span class="badge bg-success-subtle text-success-emphasis">{{ ucfirst($route->status) }}</span></div></div>
                <div class="col-12"><strong>Deskripsi:</strong><div>{{ $route->description ?? '-' }}</div></div>
            </div>
        </div>
    </div>
@endsection
