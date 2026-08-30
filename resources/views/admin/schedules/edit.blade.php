@extends('layouts.app')

@section('title', 'Edit Jadwal')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Edit Jadwal</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('schedules.index') }}">Jadwal</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('schedules.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
    </div>

    <form action="{{ route('schedules.update', $schedule) }}" method="POST">
        @csrf
        @method('PUT')
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
                                        <option value="{{ $route->id }}" {{ old('route_id', $schedule->route_id) == $route->id ? 'selected' : '' }}>{{ $route->code }} - {{ $route->name }}</option>
                                    @endforeach
                                </select>
                                @error('route_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Jadwal <span class="text-danger">*</span></label>
                                <input type="text" name="schedule_code" value="{{ old('schedule_code', $schedule->schedule_code) }}" class="form-control @error('schedule_code') is-invalid @enderror" required>
                                @error('schedule_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Hari <span class="text-danger">*</span></label>
                                <input type="text" name="day_of_week" value="{{ old('day_of_week', $schedule->day_of_week) }}" class="form-control @error('day_of_week') is-invalid @enderror" required>
                                @error('day_of_week')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jam Berangkat <span class="text-danger">*</span></label>
                                <input type="time" step="1" name="departure_time" value="{{ old('departure_time', $schedule->departure_time) }}" class="form-control @error('departure_time') is-invalid @enderror" required>
                                @error('departure_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jam Tiba <span class="text-danger">*</span></label>
                                <input type="time" step="1" name="arrival_time" value="{{ old('arrival_time', $schedule->arrival_time) }}" class="form-control @error('arrival_time') is-invalid @enderror" required>
                                @error('arrival_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Frekuensi (menit)</label>
                                <input type="number" min="1" name="frequency_minutes" value="{{ old('frequency_minutes', $schedule->frequency_minutes) }}" class="form-control @error('frequency_minutes') is-invalid @enderror">
                                @error('frequency_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $schedule->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $schedule->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $schedule->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card card-lg">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2"><i class="ti ti-check me-1"></i> Update Jadwal</button>
                        <a href="{{ route('schedules.index') }}" class="btn btn-white w-100">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
