@extends('layouts.app')

@section('title', 'Edit Kendaraan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Edit Kendaraan</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}">Vehicles</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('vehicles.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
    </div>

    <form action="{{ route('vehicles.update', $vehicle) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-xl-8">
                <div class="card card-lg mb-4">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Kode Kendaraan <span class="text-danger">*</span></label>
                                <input type="text" name="vehicle_code" value="{{ old('vehicle_code', $vehicle->vehicle_code) }}" class="form-control @error('vehicle_code') is-invalid @enderror" required>
                                @error('vehicle_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Plat Nomor <span class="text-danger">*</span></label>
                                <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" class="form-control @error('plate_number') is-invalid @enderror" required>
                                @error('plate_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kendaraan <span class="text-danger">*</span></label>
                                <input type="text" name="vehicle_type" value="{{ old('vehicle_type', $vehicle->vehicle_type) }}" class="form-control @error('vehicle_type') is-invalid @enderror" required>
                                @error('vehicle_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $vehicle->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $vehicle->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="maintenance" {{ old('status', $vehicle->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Brand</label>
                                <input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" class="form-control @error('brand') is-invalid @enderror">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Model</label>
                                <input type="text" name="model" value="{{ old('model', $vehicle->model) }}" class="form-control @error('model') is-invalid @enderror">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Warna</label>
                                <input type="text" name="color" value="{{ old('color', $vehicle->color) }}" class="form-control @error('color') is-invalid @enderror">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Tahun</label>
                                <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" class="form-control @error('year') is-invalid @enderror">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kapasitas</label>
                                <input type="number" name="capacity" value="{{ old('capacity', $vehicle->capacity) }}" class="form-control @error('capacity') is-invalid @enderror">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pemilik</label>
                                <input type="text" name="owner_name" value="{{ old('owner_name', $vehicle->owner_name) }}" class="form-control @error('owner_name') is-invalid @enderror">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card card-lg mb-4">
                    <div class="card-body">
                        <h6 class="mb-4 text-muted text-uppercase" style="font-size:11px; letter-spacing:.05em;">Foto Kendaraan</h6>
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img id="photoPreview" src="{{ $vehicle->photo ? Storage::disk('minio')->url($vehicle->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($vehicle->vehicle_code) . '&background=0d6efd&color=fff&size=128' }}" alt="Preview" class="rounded-circle object-fit-cover border" width="120" height="120" />
                                <label for="photoInput" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle p-1" style="width:30px; height:30px; cursor:pointer;">
                                    <i class="ti ti-camera" style="font-size:14px;"></i>
                                </label>
                            </div>
                        </div>
                        <input type="file" name="photo" id="photoInput" class="d-none @error('photo') is-invalid @enderror" accept="image/jpg,image/jpeg,image/png,image/webp" />
                        @error('photo')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                        @if($vehicle->photo)
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="removePhoto">
                                <label class="form-check-label" for="removePhoto">Hapus foto saat ini</label>
                            </div>
                        @endif
                        <div class="text-center mt-3">
                            <label for="photoInput" class="btn btn-white btn-sm w-100"><i class="ti ti-upload me-1"></i> Pilih Foto</label>
                        </div>
                    </div>
                </div>
                <div class="card card-lg">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2"><i class="ti ti-check me-1"></i> Update Kendaraan</button>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-white w-100">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const photoInput = document.getElementById('photoInput');
    if (photoInput) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('photoPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endpush
