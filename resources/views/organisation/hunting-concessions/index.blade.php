@extends('layouts.organisation')

@section('title', 'Hunting Concessions')

@push('styles')
    <style>
        .card-header {
            background-color: #2e7d32;
            color: white;
        }
        .hunting-concession-card {
            transition: transform 0.2s ease-in-out;
            border: none;
            height: 100%;
        }
        .hunting-concession-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .hunting-concession-card .card-title {
            font-weight: 600;
            color: #2e7d32;
        }
        .hunting-concession-card .card-body {
            padding: 1.25rem;
        }
        .hunting-concession-card .card-footer {
            background-color: rgba(46, 125, 50, 0.05);
            border-top: 1px solid rgba(46, 125, 50, 0.1);
            padding: 0.75rem 1.25rem;
        }
        .btn-group .btn {
            flex: 1;
        }
        .concession-badge {
            background-color: rgba(46, 125, 50, 0.1);
            color: #2e7d32;
            padding: 0.35rem 0.65rem;
            border-radius: 16px;
            font-size: 0.75rem;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        .concession-location {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .concession-operator {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .concession-operator i {
            margin-right: 0.5rem;
            color: #2e7d32;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mt-4">Hunting Concessions</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard.index', $organisation->slug) }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Hunting Concessions</li>
            </ol>
        </div>
        <div>
            <button class="btn btn-outline-success me-2" onclick="window.location.reload();">
                <i class="fas fa-sync me-1"></i> Refresh
            </button>
            <a href="{{ route('organisation.hunting-concessions.create', $organisation->slug ?? '') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Add New Concession
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <small>{{ session('success') }}</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <i class="fas fa-map me-1"></i>
            Hunting Concessions List
        </div>
        <div class="card-body">
            <div class="row g-3">
                @forelse($concessions as $concession)
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card hunting-concession-card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-2">{{ $concession->name }}</h5>
                                
                                @if($concession->hectarage)
                                    <div class="concession-badge">
                                        <i class="fas fa-ruler-combined me-1"></i> {{ $concession->hectarage }}
                                    </div>
                                @endif
                                
                                <div class="concession-operator">
                                    <i class="fas fa-user-tie"></i> 
                                    <span>{{ $concession->safariOperator->name ?? 'No safari operator assigned' }}</span>
                                </div>
                                
                                @if($concession->latitude && $concession->longitude)
                                    <div class="concession-location">
                                        <i class="fas fa-map-marker-alt me-1"></i> 
                                        {{ $concession->latitude }}, {{ $concession->longitude }}
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('organisation.hunting-concessions.show', [$organisation->slug ?? '', $concession]) }}" 
                                       class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('organisation.hunting-concessions.edit', [$organisation->slug ?? '', $concession]) }}" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('organisation.hunting-concessions.destroy', [$organisation->slug ?? '', $concession]) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this concession?')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> No hunting concessions found. 
                            <a href="{{ route('organisation.hunting-concessions.create', $organisation->slug ?? '') }}" class="alert-link">
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
    </div>
</div>
@endsection
