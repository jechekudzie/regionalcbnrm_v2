@extends('layouts.organisation')

@section('title', $wildlifeConflictIncident->title)

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Title Section -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item">
                        <a href="{{ route('organisation.dashboard', $organisation->slug) }}" class="text-decoration-none">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('organisation.wildlife-conflicts.index', $organisation->slug) }}" class="text-decoration-none">
                            Wildlife Conflicts
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $wildlifeConflictIncident->title }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-2">{{ $wildlifeConflictIncident->title }}</h1>
            <p class="text-muted">View detailed information about this wildlife conflict incident.</p>
        </div>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h2 class="fw-bold text-dark">{{ $wildlifeConflictIncident->title }}</h2>
            <p class="text-muted mb-1">
                <i class="fas fa-calendar-alt me-1"></i> 
                {{ $wildlifeConflictIncident->incident_date->format('d M Y') }} at {{ $wildlifeConflictIncident->incident_time->format('H:i') }}
            </p>
            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                <i class="fas fa-clock me-1"></i> {{ $wildlifeConflictIncident->period }}
            </span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('organisation.wildlife-conflicts.edit', [$organisation->slug, $wildlifeConflictIncident->id]) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Edit Incident
            </a>
            <a href="{{ route('organisation.wildlife-conflicts.index', $organisation->slug) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Grid Layout -->
    <div class="row g-4">
        <!-- Info Card -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Incident Details</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Conflict Type:</dt>
                        <dd class="col-sm-7">{{ $wildlifeConflictIncident->conflictType->name }}</dd>

                        <dt class="col-sm-5 text-muted mt-3">Species Involved:</dt>
                        <dd class="col-sm-7">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($wildlifeConflictIncident->species as $species)
                                    <span class="badge bg-success bg-opacity-10 text-success fw-semibold">
                                        <i class="fas fa-paw me-1"></i>{{ $species->name }}
                                    </span>
                                @endforeach
                            </div>
                        </dd>

                        @if($wildlifeConflictIncident->latitude && $wildlifeConflictIncident->longitude)
                            <dt class="col-sm-5 text-muted mt-3">Coordinates:</dt>
                            <dd class="col-sm-7 font-monospace">
                                {{ number_format($wildlifeConflictIncident->latitude, 6) }},
                                {{ number_format($wildlifeConflictIncident->longitude, 6) }}
                            </dd>
                        @endif

                        <dt class="col-sm-5 text-muted mt-3">Location Description:</dt>
                        <dd class="col-sm-7">{{ $wildlifeConflictIncident->location_description }}</dd>

                        <dt class="col-sm-5 text-muted mt-3">Incident Description:</dt>
                        <dd class="col-sm-7">{{ $wildlifeConflictIncident->description }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Map Card -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Location Map</h5>
                </div>
                <div class="card-body p-0">
                    @if($wildlifeConflictIncident->latitude && $wildlifeConflictIncident->longitude)
                        <div id="map" class="rounded-bottom" 
                             style="height: 500px;"
                             data-lat="{{ $wildlifeConflictIncident->latitude }}"
                             data-lng="{{ $wildlifeConflictIncident->longitude }}"
                             data-title="Wildlife Conflict Incident"
                             data-description="{{ $wildlifeConflictIncident->location_description }}">
                        </div>
                    @else
                        <div class="p-5 text-center text-muted">
                            <p class="mb-2"><i class="fas fa-map-marked-alt fa-2x"></i></p>
                            <p class="mb-2">No location data provided.</p>
                            <a href="{{ route('organisation.wildlife-conflicts.edit', [$organisation->slug, $wildlifeConflictIncident->id]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus me-1"></i> Add Location
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .breadcrumb-item a {
        color: var(--bs-primary);
    }
    
    .breadcrumb-item a:hover {
        color: var(--bs-primary);
    }
    
    .breadcrumb-item.active {
        color: #6c757d;
    }

    .badge {
        border-radius: 0.5rem;
        font-size: 0.85rem;
        padding: 0.4rem 0.75rem;
    }

    #map {
        width: 100%;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .card h5 {
        font-weight: 600;
    }

    dt {
        font-size: 0.9rem;
    }

    dd {
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapElement = document.getElementById('map');
        if (mapElement) {
            const lat = parseFloat(mapElement.dataset.lat);
            const lng = parseFloat(mapElement.dataset.lng);
            const title = mapElement.dataset.title;
            const description = mapElement.dataset.description;

            const map = L.map('map').setView([lat, lng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 18,
            }).addTo(map);

            const marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup(`<strong>${title}</strong><br>${description}`).openPopup();

            L.circle([lat, lng], {
                color: '#2563eb',
                fillColor: '#2563eb',
                fillOpacity: 0.15,
                radius: 1000
            }).addTo(map);
        }
    });
</script>
@endsection
