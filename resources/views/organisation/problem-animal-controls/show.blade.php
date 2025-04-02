@extends('layouts.organisation')

@section('title', 'Problem Animal Control Details')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border: none;
        margin-bottom: 1.5rem;
    }
    .card-header {
        background-color: #2d5a27 !important;
        color: white !important;
        border-radius: 0.5rem 0.5rem 0 0 !important;
        padding: 1rem 1.25rem;
    }
    .btn-primary, .btn-success {
        background-color: #2d5a27 !important;
        border-color: #2d5a27 !important;
    }
    .btn-primary:hover, .btn-success:hover {
        background-color: #1e3d1a !important;
        border-color: #1e3d1a !important;
    }
    .page-header {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .page-header h1 {
        margin-bottom: 0.5rem;
        color: #2d5a27;
    }
    .page-header .breadcrumb {
        margin-bottom: 0;
        background: transparent;
        padding: 0;
    }
    .page-header .breadcrumb-item a {
        color: #2d5a27;
    }
    .detail-table th {
        width: 200px;
        background-color: #f8f9fa;
        font-weight: 500;
    }
    .map-container {
        height: 300px;
        border-radius: 0.25rem;
        overflow: hidden;
    }
    .control-measures-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .control-measures-list li {
        background-color: #f8f9fa;
        padding: 0.5rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
    }
    .control-measures-list li i {
        color: #2d5a27;
        margin-right: 0.5rem;
    }
    .species-badge {
        display: inline-block;
        background-color: #2d5a27;
        color: white;
        border-radius: 0.25rem;
        padding: 0.25rem 0.5rem;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }
    .incident-link {
        color: #2d5a27;
        text-decoration: none;
        font-weight: 500;
    }
    .incident-link:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Problem Animal Control Details</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard.index', $organisation->slug) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('organisation.problem-animal-controls.index', $organisation->slug) }}">Problem Animal Control</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('organisation.problem-animal-controls.edit', [$organisation->slug, $problemAnimalControl]) }}" 
                   class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <a href="{{ route('organisation.wildlife-conflicts.show', [$organisation->slug, $problemAnimalControl->wildlifeConflictIncident]) }}" 
                   class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Incident
                </a>
                <form action="{{ route('organisation.problem-animal-controls.destroy', [$organisation->slug, $problemAnimalControl]) }}" 
                      method="POST" 
                      class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-danger" 
                            onclick="return confirm('Are you sure you want to delete this record?')">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div>
                        <i class="fas fa-shield-alt me-1"></i>
                        Problem Animal Control Information
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered detail-table">
                        <tbody>
                            <tr>
                                <th>Control Date</th>
                                <td>{{ $problemAnimalControl->control_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Control Time</th>
                                <td>{{ $problemAnimalControl->control_time ? $problemAnimalControl->control_time->format('H:i') : 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <th>Period (Year)</th>
                                <td>{{ $problemAnimalControl->period }}</td>
                            </tr>
                            <tr>
                                <th>Location</th>
                                <td>{{ $problemAnimalControl->location }}</td>
                            </tr>
                            <tr>
                                <th>Estimated Number of Animals</th>
                                <td>{{ $problemAnimalControl->estimated_number }}</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>{{ $problemAnimalControl->description ?: 'No description provided.' }}</td>
                            </tr>
                            <tr>
                                <th>Control Measures</th>
                                <td>
                                    @if($problemAnimalControl->controlMeasures->count() > 0)
                                        <ul class="control-measures-list">
                                            @foreach($problemAnimalControl->controlMeasures as $measure)
                                                <li><i class="fas fa-check-circle"></i> {{ $measure->name }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted">No control measures have been specified.</p>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Related Wildlife Conflict Incident
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="mb-2">
                            <a href="{{ route('organisation.wildlife-conflicts.show', [$organisation->slug, $problemAnimalControl->wildlifeConflictIncident]) }}" class="incident-link">
                                {{ $problemAnimalControl->wildlifeConflictIncident->title }}
                            </a>
                        </h5>
                        <p class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i> 
                            {{ $problemAnimalControl->wildlifeConflictIncident->incident_date->format('d M Y') }}
                            @if($problemAnimalControl->wildlifeConflictIncident->incident_time)
                                at {{ $problemAnimalControl->wildlifeConflictIncident->incident_time->format('H:i') }}
                            @endif
                        </p>
                        <p>{{ Str::limit($problemAnimalControl->wildlifeConflictIncident->description, 200) }}</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Location:</strong> 
                                {{ $problemAnimalControl->wildlifeConflictIncident->location_description }}
                            </div>
                            <div class="mb-3">
                                <strong>Conflict Type:</strong> 
                                {{ $problemAnimalControl->wildlifeConflictIncident->conflictType->name ?? 'Not specified' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Species Involved:</strong>
                                <div class="mt-1">
                                    @forelse($problemAnimalControl->wildlifeConflictIncident->species as $species)
                                        <span class="species-badge">{{ $species->name }}</span>
                                    @empty
                                        <span class="text-muted">No species specified</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div>
                        <i class="fas fa-map-marker-alt me-1"></i>
                        Location Map
                    </div>
                </div>
                <div class="card-body">
                    @if($problemAnimalControl->latitude && $problemAnimalControl->longitude)
                        <div id="map" 
                             data-lat="{{ $problemAnimalControl->latitude }}" 
                             data-lng="{{ $problemAnimalControl->longitude }}"
                             data-location="{{ $problemAnimalControl->location }}"
                             class="map-container"></div>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                Coordinates: {{ $problemAnimalControl->latitude }}, {{ $problemAnimalControl->longitude }}
                            </small>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-1"></i> No coordinates available for this control record.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <i class="fas fa-info-circle me-1"></i>
                        Record Information
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Created:</strong> 
                        {{ $problemAnimalControl->created_at->format('d M Y, H:i') }}
                    </p>
                    <p class="mb-0">
                        <strong>Last Updated:</strong> 
                        {{ $problemAnimalControl->updated_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var mapElement = document.getElementById('map');
        
        if (mapElement) {
            // Get coordinates from data attributes
            var latitude = parseFloat(mapElement.getAttribute('data-lat'));
            var longitude = parseFloat(mapElement.getAttribute('data-lng'));
            var locationName = mapElement.getAttribute('data-location');
            
            if (latitude && longitude) {
                var map = L.map('map').setView([latitude, longitude], 12);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                var marker = L.marker([latitude, longitude]).addTo(map);
                marker.bindPopup(locationName).openPopup();
            }
        }
    });
</script>
@endpush 