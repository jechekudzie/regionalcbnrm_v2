@extends('layouts.organisation')

@section('title', 'CBNRM - Wildlife and Conservation Species')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">CBNRM - Wildlife and Conservation Species</h2>
                <div>
                    <button class="btn btn-info" onclick="window.location.reload();">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                    <a href="{{ route('species.create', $organisation->slug ?? '') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Species
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-3">
        @forelse($species as $specie)
            <div class="col-sm-6 col-md-4 col-lg-4 col-xl-3">
                <div class="card h-100">
                    <div class="position-relative">
                        <img src="{{ $specie->avatar ? asset($specie->avatar) : asset('images/default-species.jpg') }}" 
                             class="card-img-top" alt="{{ $specie->name }}" 
                             style="height: 200px; object-fit: cover;">
                        @if($specie->is_special)
                            <div class="position-absolute top-0 end-0 p-2">
                                <span class="badge bg-warning">
                                    Special
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <h5 class="card-title mb-1">{{ $specie->name }}</h5>
                        @if($specie->scientific)
                            <p class="text-muted small mb-2"><em>{{ $specie->scientific }}</em></p>
                        @endif
                        @if($specie->male_name || $specie->female_name)
                            <div class="small text-muted mb-2">
                                @if($specie->male_name)
                                    <div>♂ {{ $specie->male_name }}</div>
                                @endif
                                @if($specie->female_name)
                                    <div>♀ {{ $specie->female_name }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('species.show', [$organisation->slug ?? '', $specie]) }}" class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('species.edit', [$organisation->slug ?? '', $specie]) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('species.destroy', [$organisation->slug ?? '', $specie]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Are you sure you want to delete this species?')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No species found. <a href="{{ route('species.create', $organisation->slug ?? '') }}" class="alert-link">Add your first species</a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $species->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>

<style>
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .btn-group .btn {
        flex: 1;
    }
</style>
@endsection
