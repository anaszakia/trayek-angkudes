@extends('layouts.app')
@section('title', 'Edit Titik Trayek')
@section('content')<div class="d-flex justify-content-between align-items-center mb-6"><h4>Edit Titik Trayek</h4><a href="{{ route('route-points.index') }}" class="btn btn-white">Kembali</a></div><form action="{{ route('route-points.update', $routePoint) }}" method="POST">@method('PUT')<div class="card card-lg"><div class="card-body">@include('admin.route-points.form')</div><div class="card-footer"><button class="btn btn-primary">Simpan Perubahan</button></div></div></form>@endsection
