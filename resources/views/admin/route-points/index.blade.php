@extends('layouts.app')

@section('title', 'Titik Trayek')

@section('content')
	<div class="d-flex justify-content-between align-items-center mb-6">
		<div>
			<h4 class="mb-0">Titik Trayek</h4>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
					<li class="breadcrumb-item active">Titik Trayek</li>
				</ol>
			</nav>
		</div>
		@if(can('route_points.create'))
			<a href="{{ route('route-points.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Tambah Titik</a>
		@endif
	</div>

	<div class="card card-lg">
		<div class="card-body p-0">
			<div class="p-4 border-bottom">
				<form method="GET">
					<div class="input-group col-md-6">
						<span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
						<input name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama titik">
						<button class="btn btn-primary">Cari</button>
					</div>
				</form>
			</div>

			<div class="table-responsive">
				<table class="table table-hover table-centered mb-0">
					<thead><tr><th>#</th><th>Trayek</th><th>Urutan</th><th>Nama</th><th>Koordinat</th><th>Terminal</th><th>Aksi</th></tr></thead>
					<tbody>
						@forelse($points as $point)
							<tr>
								<td>{{ $points->firstItem() + $loop->index }}</td>
								<td>{{ $point->route?->code }}</td>
								<td>{{ $point->sequence }}</td>
								<td>{{ $point->name }}</td>
								<td>{{ $point->latitude ?? '-' }}, {{ $point->longitude ?? '-' }}</td>
								<td>{{ $point->is_terminal ? 'Ya' : 'Tidak' }}</td>
								<td>
									<div class="d-flex gap-2">
										<a href="{{ route('route-points.show', $point) }}" class="btn btn-sm btn-white" title="Detail"><i class="ti ti-eye"></i></a>
										@if(can('route_points.edit'))
											<a href="{{ route('route-points.edit', $point) }}" class="btn btn-sm btn-white" title="Edit"><i class="ti ti-edit"></i></a>
										@endif
										@if(can('route_points.delete'))
											<form action="{{ route('route-points.destroy', $point) }}" method="POST">
												@csrf
												@method('DELETE')
												<button type="button" class="btn btn-sm btn-white text-danger" data-confirm="Yakin hapus titik ini?" title="Hapus"><i class="ti ti-trash"></i></button>
											</form>
										@endif
									</div>
								</td>
							</tr>
						@empty
							<tr><td colspan="7" class="text-center text-muted py-6">Belum ada titik trayek.</td></tr>
						@endforelse
					</tbody>
				</table>
			</div>

			@if($points->hasPages())
				<div class="px-4 py-3 border-top">{{ $points->links() }}</div>
			@endif
		</div>
	</div>
@endsection
