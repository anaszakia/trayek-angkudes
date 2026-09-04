@extends('layouts.app')
@section('title', 'Tambah Halte')
@section('content')<div class="d-flex justify-content-between align-items-center mb-6"><h4>Tambah Halte</h4><a href="{{ route('route-stops.index') }}" class="btn btn-white">Kembali</a></div><form action="{{ route('route-stops.store') }}" method="POST"><div class="card card-lg"><div class="card-body">@include('admin.route-stops.form')</div><div class="card-footer"><button class="btn btn-primary">Simpan Halte</button></div></div></form>@endsection
