@extends('layouts.app')

@section('title', 'Trayek Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Trayek Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Trayek</li>
                </ol>
            </nav>
        </div>
        @if(can('routes.create'))
            <a href="{{ route('routes.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Tambah Trayek</a>
        @endif
    </div>

    <div class="card card-lg">
        <div class="card-body p-0">
            <div class="p-4 border-bottom">
                <form action="{{ route('routes.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari kode atau nama trayek">
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('routes.index') }}" class="btn btn-white">Reset</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama Trayek</th>
                            <th>Rute</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($routes as $route)
                            <tr>
                                <td>{{ $routes->firstItem() + $loop->index }}</td>
                                <td>{{ $route->code }}</td>
                                <td>{{ $route->name }}</td>
                                <td>{{ $route->start_point }} - {{ $route->end_point }}</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'active' => 'bg-success-subtle text-success-emphasis',
                                            'inactive' => 'bg-secondary-subtle text-secondary-emphasis',
                                            'maintenance' => 'bg-warning-subtle text-warning-emphasis',
                                        ][$route->status] ?? 'bg-light text-dark';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($route->status) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if(can('routes.view'))
                                            <a href="{{ route('routes.show', $route) }}" class="btn btn-sm btn-white" title="Detail"><i class="ti ti-eye"></i></a>
                                        @endif
                                        @if(can('routes.edit'))
                                            <a href="{{ route('routes.edit', $route) }}" class="btn btn-sm btn-white" title="Edit"><i class="ti ti-edit"></i></a>
                                        @endif
                                        @if(can('routes.delete'))
                                            <form action="{{ route('routes.destroy', $route) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-white text-danger" data-confirm="Yakin hapus trayek {{ $route->name }}?" title="Hapus"><i class="ti ti-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-6">
                                    @if(request('search'))
                                        Trayek tidak ditemukan.
                                    @else
                                        Belum ada trayek.
                                        <a href="{{ route('routes.create') }}">Tambah sekarang</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($routes->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $routes->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
