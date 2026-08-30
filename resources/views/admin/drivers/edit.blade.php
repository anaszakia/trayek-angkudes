@extends('layouts.app')

@section('title', 'Edit Driver')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Edit Driver</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('drivers.index') }}">Drivers</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('drivers.index') }}" class="btn btn-white"><i class="ti ti-arrow-left me-1"></i> Kembali</a>
    </div>

    <form action="{{ route('drivers.update', $driver) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-xl-8">
                <div class="card card-lg mb-4">
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">User <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Pilih User --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $driver->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Kode Driver <span class="text-danger">*</span></label>
                                <input type="text" name="driver_code" value="{{ old('driver_code', $driver->driver_code) }}" class="form-control @error('driver_code') is-invalid @enderror" required>
                                @error('driver_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $driver->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $driver->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status', $driver->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">NIK</label>
                                <input type="text" name="nik" value="{{ old('nik', $driver->nik) }}" class="form-control @error('nik') is-invalid @enderror">
                                @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="phone" value="{{ old('phone', $driver->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nomor SIM</label>
                                <input type="text" name="license_number" value="{{ old('license_number', $driver->license_number) }}" class="form-control @error('license_number') is-invalid @enderror">
                                @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis SIM</label>
                                <input type="text" name="license_type" value="{{ old('license_type', $driver->license_type) }}" class="form-control @error('license_type') is-invalid @enderror">
                                @error('license_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $driver->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card card-lg mb-4">
                    <div class="card-body">
                        <h6 class="mb-4 text-muted text-uppercase" style="font-size:11px; letter-spacing:.05em;">Foto Driver</h6>
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img id="photoPreview" src="{{ $driver->photo ? Storage::disk('minio')->url($driver->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($driver->user?->name ?? $driver->driver_code) . '&background=0d6efd&color=fff&size=128' }}" alt="Preview" class="rounded-circle object-fit-cover border" width="120" height="120" />
                                <label for="photoInput" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle p-1" style="width:30px; height:30px; cursor:pointer;">
                                    <i class="ti ti-camera" style="font-size:14px;"></i>
                                </label>
                            </div>
                        </div>
                        <input type="file" name="photo" id="photoInput" class="d-none @error('photo') is-invalid @enderror" accept="image/jpg,image/jpeg,image/png,image/webp" />
                        @error('photo')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                        @if($driver->photo)
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
                        <button type="submit" class="btn btn-primary w-100 mb-2"><i class="ti ti-check me-1"></i> Update Driver</button>
                        <a href="{{ route('drivers.index') }}" class="btn btn-white w-100">Batal</a>
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
