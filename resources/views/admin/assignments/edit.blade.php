@extends('layouts.app')

@section('title', 'Edit Assignment')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Edit Assignment</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('assignments.index') }}">Assignments</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('assignments.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <form action="{{ route('assignments.update', $assignment) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Driver <span class="text-danger">*</span></label>
                        <select name="driver_id" class="form-select @error('driver_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Driver --</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ old('driver_id', $assignment->driver_id) == $driver->id ? 'selected' : '' }}>{{ $driver->user?->name ?? $driver->driver_code }} - {{ $driver->driver_code }}</option>
                            @endforeach
                        </select>
                        @error('driver_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kendaraan <span class="text-danger">*</span></label>
                        <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kendaraan --</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $assignment->vehicle_id) == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->plate_number }} - {{ $vehicle->vehicle_code }}</option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="started_at" value="{{ old('started_at', $assignment->started_at ? $assignment->started_at->format('Y-m-d\TH:i') : '') }}" class="form-control @error('started_at') is-invalid @enderror" required>
                        @error('started_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Selesai</label>
                        <input type="datetime-local" name="ended_at" value="{{ old('ended_at', $assignment->ended_at ? $assignment->ended_at->format('Y-m-d\TH:i') : '') }}" class="form-control @error('ended_at') is-invalid @enderror">
                        @error('ended_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $assignment->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="ended" {{ old('status', $assignment->status) == 'ended' ? 'selected' : '' }}>Ended</option>
                            <option value="cancelled" {{ old('status', $assignment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="ti ti-check me-1"></i> Update Assignment</button>
                        <a href="{{ route('assignments.index') }}" class="btn btn-white">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
