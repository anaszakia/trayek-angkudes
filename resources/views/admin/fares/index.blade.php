@extends('layouts.app')

@section('title', 'Tarif Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Tarif Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tarif</li>
                </ol>
            </nav>
        </div>
        @if(can('fares.create'))
            <a href="{{ route('fares.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Tambah Tarif</a>
        @endif
    </div>

    <div class="card card-lg">
        <div class="card-body p-0">
            <div class="p-4 border-bottom">
                <form action="{{ route('fares.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari kode tarif, nama, atau trayek">
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('fares.index') }}" class="btn btn-white">Reset</a>
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
                            <th>Kode Tarif</th>
                            <th>Nama</th>
                            <th>Trayek</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fares as $fare)
                            <tr>
                                <td>{{ $fares->firstItem() + $loop->index }}</td>
                                <td>{{ $fare->fare_code }}</td>
                                <td>{{ $fare->name }}</td>
                                <td>{{ $fare->route?->name ?? '-' }}</td>
                                <td>{{ number_format($fare->amount, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'active' => 'bg-success-subtle text-success-emphasis',
                                            'inactive' => 'bg-secondary-subtle text-secondary-emphasis',
                                        ][$fare->status] ?? 'bg-light text-dark';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($fare->status) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if(can('fares.view'))
                                            <a href="{{ route('fares.show', $fare) }}" class="btn btn-sm btn-white" title="Detail"><i class="ti ti-eye"></i></a>
                                        @endif
                                        @if(can('fares.edit'))
                                            <a href="{{ route('fares.edit', $fare) }}" class="btn btn-sm btn-white" title="Edit"><i class="ti ti-edit"></i></a>
                                        @endif
                                        @if(can('fares.delete'))
                                            <form action="{{ route('fares.destroy', $fare) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-white text-danger" data-confirm="Yakin hapus tarif {{ $fare->fare_code }}?" title="Hapus"><i class="ti ti-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-6">
                                    @if(request('search'))
                                        Tarif tidak ditemukan.
                                    @else
                                        Belum ada tarif.
                                        <a href="{{ route('fares.create') }}">Tambah sekarang</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($fares->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $fares->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
