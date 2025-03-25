@extends('layouts.organisation')

@section('title', 'Hunting Concessions')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Hunting Concessions</h2>
                <div>
                    <button class="btn btn-info" onclick="window.location.reload();">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                    <a href="{{ route('organisation.hunting-concessions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Concession
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
        @forelse($concessions as $concession)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-1">{{ $concession->name }}</h5>
                        @if($concession->hectarage)
                            <p class="text-muted small mb-2">
                                <i class="fas fa-ruler-combined"></i> {{ $concession->hectarage }} hectares
                            </p>
                        @endif
                        @if($concession->description)
                            <p class="small mb-2">{{ Str::limit($concession->description, 100) }}</p>
                        @endif
                        @if($concession->latitude && $concession->longitude)
                            <p class="text-muted small mb-2">
                                <i class="fas fa-map-marker-alt"></i> 
                                {{ $concession->latitude }}, {{ $concession->longitude }}
                            </p>
                        @endif
                        @if($concession->safari)
                            <p class="text-muted small mb-0">
                                <i class="fas fa-user"></i> Safari Operator: {{ $concession->safari->name }}
                            </p>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('organisation.hunting-concessions.show', $concession) }}" 
                               class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('organisation.hunting-concessions.edit', $concession) }}" 
                               class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('organisation.hunting-concessions.destroy', $concession) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Are you sure you want to delete this concession?')" 
                                        title="Delete">
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
                    No hunting concessions found. 
                    <a href="{{ route('organisation.hunting-concessions.create') }}" class="alert-link">
                        Add your first concession
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $concessions->withQueryString()->links('pagination::bootstrap-5') }}
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