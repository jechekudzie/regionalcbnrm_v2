@extends('layouts.organisation')

@section('styles')
<style>
    .card-header {
        background-color: #2d5a27 !important;
        color: white !important;
    }
    .btn-primary {
        background-color: #2d5a27 !important;
        border-color: #2d5a27 !important;
    }
    .btn-primary:hover {
        background-color: #1e3d1a !important;
        border-color: #1e3d1a !important;
    }
    .badge-success {
        background-color: #2d5a27 !important;
        color: white !important;
    }
    .badge-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
    }
    .table > tbody > tr:hover {
        background-color: #f8f9fa;
    }
    .incident-title {
        color: #2d5a27;
        font-weight: 500;
    }
    .incident-title:hover {
        color: #1e3d1a;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .btn-group .btn i {
        font-size: 0.875rem;
    }
    .table-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-right: 0.75rem;
    }
    .icon-warning {
        background-color: #fff3cd;
        color: #856404;
    }
    .breadcrumb {
        margin-bottom: 1rem;
    }
    .breadcrumb-item a {
        color: #2d5a27;
        text-decoration: none;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }
    .export-buttons {
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }
    .export-buttons .btn {
        margin-right: 0.5rem;
        font-size: 0.875rem;
    }
    .search-box {
        margin-bottom: 1rem;
    }
    .refresh-btn {
        color: #2d5a27;
        border-color: #2d5a27;
    }
    .refresh-btn:hover {
        background-color: #2d5a27;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Wildlife Conflict Incidents</h1>
                <a href="{{ route('organisation.wildlife-conflicts.create', $organisation->slug) }}" 
                   class="btn btn-success">
                    <i class="fas fa-plus"></i> Record New Incident
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <a href="#" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-print"></i> Print
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Location</th>
                                    <th>Conflict Type</th>
                                    <th>Species</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($wildlifeConflictIncidents as $wildlifeConflictIncident)
                                    <tr>
                                        <td>
                                            <div class="fw-medium">{{ $wildlifeConflictIncident->incident_date->format('d M Y') }}</div>
                                            <div class="text-muted small">{{ $wildlifeConflictIncident->incident_time->format('H:i') }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $wildlifeConflictIncident->location_description }}</div>
                                            @if($wildlifeConflictIncident->latitude && $wildlifeConflictIncident->longitude)
                                                <div class="text-muted small">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    {{ number_format($wildlifeConflictIncident->latitude, 6) }}, 
                                                    {{ number_format($wildlifeConflictIncident->longitude, 6) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                {{ $wildlifeConflictIncident->conflictType->name }}
                                            </span>
                                        </td>
                                        <td>
                                            @foreach($wildlifeConflictIncident->species as $species)
                                                <span class="badge bg-success">{{ $species->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('organisation.wildlife-conflicts.show', ['organisation' => $organisation->slug, 'wildlifeConflictIncident' => $wildlifeConflictIncident]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('organisation.wildlife-conflicts.edit', ['organisation' => $organisation->slug, 'wildlifeConflictIncident' => $wildlifeConflictIncident]) }}" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('organisation.wildlife-conflicts.destroy', ['organisation' => $organisation->slug, 'wildlifeConflictIncident' => $wildlifeConflictIncident]) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this incident?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-info-circle text-muted fa-2x mb-3"></i>
                                            <h5 class="text-muted">No Wildlife Conflict Incidents</h5>
                                            <p class="text-muted mb-3">No wildlife conflict incidents have been recorded yet.</p>
                                            <a href="{{ route('organisation.wildlife-conflicts.create', $organisation->slug) }}" 
                                               class="btn btn-primary">
                                                Record First Incident
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($wildlifeConflictIncidents->hasPages())
                        <div class="mt-4">
                            {{ $wildlifeConflictIncidents->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 