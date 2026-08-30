@extends('layouts.app')

@section('title', 'Tambah Tarif')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Tambah Tarif</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('fares.index') }}">Tarif</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('fares.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
    </div>

    <form action="{{ route('fares.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-xl-8">
                <div class="card card-lg mb-4">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Trayek <span class="text-danger">*</span></label>
                                <select name="route_id" class="form-select @error('route_id') is-invalid @enderror" required>
                                    <option value="">Pilih trayek</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>{{ $route->code }} - {{ $route->name }}</option>
                                    @endforeach
                                </select>
                                @error('route_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Tarif <span class="text-danger">*</span></label>
                                <input type="text" name="fare_code" value="{{ old('fare_code') }}" class="form-control @error('fare_code') is-invalid @enderror" required>
                                @error('fare_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nama Tarif <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipe Penumpang <span class="text-danger">*</span></label>
                                <select name="passenger_type" class="form-select @error('passenger_type') is-invalid @enderror" required>
                                    <option value="general" {{ old('passenger_type') == 'general' ? 'selected' : '' }}>Umum</option>
                                    <option value="student" {{ old('passenger_type') == 'student' ? 'selected' : '' }}>Pelajar</option>
                                    <option value="senior" {{ old('passenger_type') == 'senior' ? 'selected' : '' }}>Lansia</option>
                                    <option value="disabled" {{ old('passenger_type') == 'disabled' ? 'selected' : '' }}>Disabilitas</option>
                                    <option value="children" {{ old('passenger_type') == 'children' ? 'selected' : '' }}>Anak</option>
                                </select>
                                @error('passenger_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" required>
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mata Uang</label>
                                <input type="text" name="currency" value="{{ old('currency', 'IDR') }}" class="form-control @error('currency') is-invalid @enderror">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Berlaku Dari</label>
                                <input type="date" name="effective_from" value="{{ old('effective_from') }}" class="form-control @error('effective_from') is-invalid @enderror">
                                @error('effective_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Berlaku Sampai</label>
                                <input type="date" name="effective_to" value="{{ old('effective_to') }}" class="form-control @error('effective_to') is-invalid @enderror">
                                @error('effective_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card card-lg">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2"><i class="ti ti-check me-1"></i> Simpan Tarif</button>
                        <a href="{{ route('fares.index') }}" class="btn btn-white w-100">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
