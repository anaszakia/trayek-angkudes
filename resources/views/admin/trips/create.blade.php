@extends('layouts.app')

@section('title', 'Tambah Trip')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Tambah Trip</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('trips.index') }}">Trip</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('trips.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
    </div>

    <form action="{{ route('trips.store') }}" method="POST">
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
                                <label class="form-label">Kode Trip <span class="text-danger">*</span></label>
                                <input type="text" name="trip_code" value="{{ old('trip_code') }}" class="form-control @error('trip_code') is-invalid @enderror" required>
                                @error('trip_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Driver</label>
                                <select name="driver_id" class="form-select @error('driver_id') is-invalid @enderror">
                                    <option value="">Pilih driver</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->driver_code }} - {{ $driver->user?->name ?? '-' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kendaraan</label>
                                <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror">
                                    <option value="">Pilih kendaraan</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->vehicle_code }} - {{ $vehicle->plate_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jadwal</label>
                                <select name="schedule_id" class="form-select @error('schedule_id') is-invalid @enderror">
                                    <option value="">Pilih jadwal</option>
                                    @foreach($schedules as $schedule)
                                        <option value="{{ $schedule->id }}" {{ old('schedule_id') == $schedule->id ? 'selected' : '' }}>{{ $schedule->schedule_code }} - {{ $schedule->day_of_week }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="scheduled" {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mulai</label>
                                <input type="datetime-local" name="started_at" value="{{ old('started_at') }}" class="form-control @error('started_at') is-invalid @enderror">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Selesai</label>
                                <input type="datetime-local" name="ended_at" value="{{ old('ended_at') }}" class="form-control @error('ended_at') is-invalid @enderror">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Total Penumpang</label>
                                <input type="number" min="0" name="total_passengers" value="{{ old('total_passengers', 0) }}" class="form-control @error('total_passengers') is-invalid @enderror">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card card-lg">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2"><i class="ti ti-check me-1"></i> Simpan Trip</button>
                        <a href="{{ route('trips.index') }}" class="btn btn-white w-100">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
