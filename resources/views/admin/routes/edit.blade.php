@extends('layouts.app')

@section('title', 'Edit Trayek')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Edit Trayek</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('routes.index') }}">Trayek</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('routes.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
    </div>

    <form action="{{ route('routes.update', $route) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-xl-8">
                <div class="card card-lg mb-4">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Kode Trayek <span class="text-danger">*</span></label>
                                <input type="text" name="code" value="{{ old('code', $route->code) }}" class="form-control @error('code') is-invalid @enderror" required>
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Trayek <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $route->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Tipe Trayek <span class="text-danger">*</span></label>
                                <select name="route_type" class="form-select @error('route_type') is-invalid @enderror" required>
                                    <option value="one_way" {{ old('route_type', $route->route_type) == 'one_way' ? 'selected' : '' }}>One Way</option>
                                    <option value="round_trip" {{ old('route_type', $route->route_type) == 'round_trip' ? 'selected' : '' }}>Round Trip</option>
                                    <option value="loop" {{ old('route_type', $route->route_type) == 'loop' ? 'selected' : '' }}>Loop</option>
                                </select>
                                @error('route_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $route->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $route->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="maintenance" {{ old('status', $route->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jarak (KM)</label>
                                <input type="number" step="0.01" name="distance_km" value="{{ old('distance_km', $route->distance_km) }}" class="form-control @error('distance_km') is-invalid @enderror">
                                @error('distance_km')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Titik Awal <span class="text-danger">*</span></label>
                                <input type="text" name="start_point" value="{{ old('start_point', $route->start_point) }}" class="form-control @error('start_point') is-invalid @enderror" required>
                                @error('start_point')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Titik Akhir <span class="text-danger">*</span></label>
                                <input type="text" name="end_point" value="{{ old('end_point', $route->end_point) }}" class="form-control @error('end_point') is-invalid @enderror" required>
                                @error('end_point')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $route->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card card-lg">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2"><i class="ti ti-check me-1"></i> Update Trayek</button>
                        <a href="{{ route('routes.index') }}" class="btn btn-white w-100">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
