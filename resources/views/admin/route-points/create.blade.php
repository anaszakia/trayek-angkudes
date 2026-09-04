@extends('layouts.app')
@section('title', 'Tambah Titik Trayek')
@section('content')<div class="d-flex justify-content-between align-items-center mb-6"><h4>Tambah Titik Trayek</h4><a href="{{ route('route-points.index') }}" class="btn btn-white">Kembali</a></div><form action="{{ route('route-points.store') }}" method="POST"><div class="card card-lg"><div class="card-body">@include('admin.route-points.form')</div><div class="card-footer"><button class="btn btn-primary">Simpan Titik</button></div></div></form>@endsection
