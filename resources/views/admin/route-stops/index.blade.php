@extends('layouts.app')

@section('title', 'Halte')

@section('content')
	<div class="d-flex justify-content-between align-items-center mb-6">
		<div>
			<h4 class="mb-0">Halte</h4>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
					<li class="breadcrumb-item active">Halte</li>
				</ol>
			</nav>
		</div>
		@if(can('route_stops.create'))
			<a href="{{ route('route-stops.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Tambah Halte</a>
		@endif
	</div>

	<div class="card card-lg">
		<div class="card-body p-0">
			<div class="p-4 border-bottom">
				<form method="GET">
					<div class="input-group col-md-6">
						<span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
						<input name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama halte">
						<button class="btn btn-primary">Cari</button>
					</div>
				</form>
			</div>

			<div class="table-responsive">
				<table class="table table-hover table-centered mb-0">
					<thead><tr><th>#</th><th>Trayek</th><th>Urutan</th><th>Nama</th><th>Koordinat</th><th>Status</th><th>Aksi</th></tr></thead>
					<tbody>
						@forelse($stops as $stop)
							<tr>
								<td>{{ $stops->firstItem() + $loop->index }}</td>
								<td>{{ $stop->route?->code }}</td>
								<td>{{ $stop->sequence }}</td>
								<td>{{ $stop->name }}</td>
								<td>{{ $stop->latitude ?? '-' }}, {{ $stop->longitude ?? '-' }}</td>
								<td>{{ $stop->is_active ? 'Aktif' : 'Tidak aktif' }}</td>
								<td>
									<div class="d-flex gap-2">
										<a href="{{ route('route-stops.show', $stop) }}" class="btn btn-sm btn-white" title="Detail"><i class="ti ti-eye"></i></a>
										@if(can('route_stops.edit'))
											<a href="{{ route('route-stops.edit', $stop) }}" class="btn btn-sm btn-white" title="Edit"><i class="ti ti-edit"></i></a>
										@endif
										@if(can('route_stops.delete'))
											<form action="{{ route('route-stops.destroy', $stop) }}" method="POST">
												@csrf
												@method('DELETE')
												<button type="button" class="btn btn-sm btn-white text-danger" data-confirm="Yakin hapus halte ini?" title="Hapus"><i class="ti ti-trash"></i></button>
											</form>
										@endif
									</div>
								</td>
							</tr>
						@empty
							<tr><td colspan="7" class="text-center text-muted py-6">Belum ada halte.</td></tr>
						@endforelse
					</tbody>
				</table>
			</div>

			@if($stops->hasPages())
				<div class="px-4 py-3 border-top">{{ $stops->links() }}</div>
			@endif
		</div>
	</div>
@endsection
