@extends('layouts.organisation')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.12);
        margin-bottom: 1.5rem;
        border: none;
    }
    .card-header {
        background-color: #2d5a27 !important;
        color: white !important;
        border-radius: 0.5rem 0.5rem 0 0 !important;
        font-weight: 600;
        padding: 1rem 1.25rem;
    }
    .card-header h5 {
        color: #212529 !important;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.5em 0.85em;
        border-radius: 0.25rem;
        font-weight: 500;
    }
    .method-badge {
        background-color: #e9ecef;
        color: #212529;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        display: inline-block;
        border: 1px solid rgba(0,0,0,0.1);
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
    .species-badge {
        background-color: #2d5a27;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        display: inline-block;
    }
    .icon-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(45, 90, 39, 0.1);
        color: #2d5a27;
        margin-right: 10px;
    }
    .detail-table tr {
        border-color: rgba(45, 90, 39, 0.1);
    }
    .detail-table th {
        width: 200px;
        border-right: 1px solid rgba(45, 90, 39, 0.1);
        vertical-align: middle;
        font-weight: 600;
        color: #6c757d;
    }
    .detail-table td {
        vertical-align: middle;
        padding: 0.75rem;
    }
    .location-badge {
        background-color: rgba(45, 90, 39, 0.1);
        color: #2d5a27;
        padding: 0.35rem 0.65rem;
        border-radius: 16px;
        font-size: 0.9rem;
        display: inline-block;
    }
    .no-data {
        padding: 3rem 2rem;
        text-align: center;
        color: #6c757d;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
    }
    .no-data i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: #adb5bd;
    }
    .no-data p {
        font-size: 1.1rem;
        font-weight: 500;
    }
    .timeline-item {
        padding: 0.75rem 1rem;
        border-left: 3px solid #2d5a27;
        margin-bottom: 0.75rem;
        background-color: #f8f9fa;
        border-radius: 0 0.25rem 0.25rem 0;
    }
    .methods-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 0.75rem 0;
    }
    .poacher-card {
        transition: all 0.2s ease;
        border: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .poacher-card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    .poacher-header-content {
        display: flex;
        align-items: center;
    }
    .poacher-avatar {
        width: 40px;
        height: 40px;
        background-color: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        color: #495057;
        font-weight: 600;
    }
    /* Map marker pulse animation */
    @keyframes pulse {
        0% {
            transform: scale(0.5);
            opacity: 1;
        }
        100% {
            transform: scale(1.5);
            opacity: 0;
        }
    }
    
    .custom-div-icon {
        background-color: #2d5a27;
        color: white;
        border-radius: 50%;
        text-align: center;
        line-height: 30px;
        font-weight: bold;
    }
    
    .pulse-marker {
        position: absolute;
        width: 40px;
        height: 40px;
        margin-left: -20px;
        margin-top: -20px;
        border-radius: 50%;
        background: rgba(45, 90, 39, 0.2);
        animation: pulse 2s infinite;
        pointer-events: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="card bg-nature-green shadow-sm mb-4" style="background-color: #2d5a27 !important;">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-0 text-white" style="color: white !important;">{{ $poachingIncident->title }}</h3>
                            <p class="mb-0 opacity-75 text-white" style="color: white !important;">
                                <i class="fas fa-exclamation-triangle me-1"></i> Poaching Incident Details
                                <span class="ms-3 badge bg-warning text-dark">{{ $poachingIncident->period }}</span>
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('organisation.poaching-incidents.edit', ['organisation' => $organisation->slug, 'poachingIncident' => $poachingIncident]) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('organisation.poaching-incidents.index', $organisation->slug) }}" class="btn btn-outline-warning">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <span class="icon-circle bg-white" style="background-color: white !important;">
                                    <i class="fas fa-info" style="color: #2d5a27 !important;"></i>
                                </span>
                                <h5 class="mb-0 fw-bold text-dark">Incident Information</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table detail-table">
                                <tr>
                                    <th>Title</th>
                                    <td class="fw-medium">{{ $poachingIncident->title }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ $poachingIncident->date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Time</th>
                                    <td>{{ $poachingIncident->time->format('H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td>{{ $poachingIncident->location ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Coordinates</th>
                                    <td>
                                        @if($poachingIncident->latitude && $poachingIncident->longitude)
                                            <span class="bg-light px-2 py-1 rounded font-monospace">
                                                <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                                {{ number_format($poachingIncident->latitude, 6) }}, {{ number_format($poachingIncident->longitude, 6) }}
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic">Not specified</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Docket Number</th>
                                    <td>
                                        @if($poachingIncident->docket_number)
                                            <span class="fw-medium">{{ $poachingIncident->docket_number }}</span>
                                        @else
                                            <span class="text-muted fst-italic">Not specified</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Docket Status</th>
                                    <td>
                                        @if($poachingIncident->docket_status)
                                            <span class="badge bg-secondary">{{ ucfirst($poachingIncident->docket_status) }}</span>
                                        @else
                                            <span class="text-muted fst-italic">Not specified</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $poachingIncident->created_at->format('M d, Y H:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $poachingIncident->updated_at->format('M d, Y H:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Map Card -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <span class="icon-circle bg-white" style="background-color: white !important;">
                                    <i class="fas fa-map-marker-alt" style="color: #2d5a27 !important;"></i>
                                </span>
                                <h5 class="mb-0 fw-bold text-dark">Location Map</h5>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if($poachingIncident->latitude && $poachingIncident->longitude)
                                <div id="map" 
                                     data-lat="{{ $poachingIncident->latitude }}"
                                     data-lng="{{ $poachingIncident->longitude }}"
                                     data-title="{{ $poachingIncident->title }}"
                                     data-description="{{ $poachingIncident->location ?? 'Poaching incident location' }}"
                                     style="height: 400px; width: 100%; border-radius: 0 0 4px 4px;"></div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                    <p>No location data available for this incident</p>
                                    <a href="{{ route('organisation.poaching-incidents.edit', ['organisation' => $organisation->slug, 'poachingIncident' => $poachingIncident]) }}" 
                                       class="btn btn-outline-success mt-3">
                                        <i class="fas fa-edit me-1"></i> Add Location
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <span class="icon-circle bg-white" style="background-color: white !important;">
                                    <i class="fas fa-paw" style="color: #2d5a27 !important;"></i>
                                </span>
                                <h5 class="mb-0 fw-bold text-dark">Species Information</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($poachingIncident->species->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Species</th>
                                                <th class="text-end">Estimated Number</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($poachingIncident->species as $species)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-success me-2">{{ $species->name }}</span>
                                                    </td>
                                                    <td class="text-end">
                                                        @if($species->pivot->estimate_number)
                                                            <span class="badge bg-dark">{{ $species->pivot->estimate_number }}</span>
                                                        @else
                                                            <span class="text-muted">Not specified</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="no-data">
                                    <i class="fas fa-info-circle"></i>
                                    <p>No species information available</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <span class="icon-circle bg-white" style="background-color: white !important;">
                                    <i class="fas fa-tools" style="color: #2d5a27 !important;"></i>
                                </span>
                                <h5 class="mb-0 fw-bold text-dark">Poaching Methods</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($poachingIncident->methods->count() > 0)
                                <div class="methods-container">
                                    @foreach($poachingIncident->methods as $method)
                                        <div class="method-badge">
                                            <i class="fas fa-check-circle me-1 text-success"></i> {{ $method->name }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="no-data">
                                    <i class="fas fa-info-circle"></i>
                                    <p>No poaching methods recorded</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="icon-circle bg-white" style="background-color: white !important;">
                            <i class="fas fa-users" style="color: #2d5a27 !important;"></i>
                        </span>
                        <h5 class="mb-0 fw-bold text-dark">Poachers Information</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($poachingIncident->poachers->count() > 0)
                        <div class="row">
                            @foreach($poachingIncident->poachers as $poacher)
                                <div class="col-md-6 mb-4">
                                    <div class="card poacher-card">
                                        <div class="card-header">
                                            <div class="poacher-header-content">
                                                <div class="poacher-avatar">
                                                    {{ strtoupper(substr($poacher->first_name, 0, 1)) }}{{ strtoupper(substr($poacher->last_name, 0, 1)) }}
                                                </div>
                                                <h6 class="mb-0 text-dark">{{ $poacher->full_name }}</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm">
                                                <tbody>
                                                    <tr>
                                                        <th>Age</th>
                                                        <td>{{ $poacher->age ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status</th>
                                                        <td>
                                                            @if($poacher->status)
                                                                @php
                                                                    $statusClass = match($poacher->status) {
                                                                        'suspected' => 'bg-warning text-dark',
                                                                        'arrested' => 'bg-danger',
                                                                        'bailed' => 'bg-info',
                                                                        'sentenced' => 'bg-dark',
                                                                        'released' => 'bg-success',
                                                                        default => 'bg-secondary'
                                                                    };
                                                                @endphp
                                                                <span class="badge {{ $statusClass }}">{{ ucfirst($poacher->status) }}</span>
                                                            @else
                                                                <span class="text-muted">Not specified</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Poacher Type</th>
                                                        <td>{{ $poacher->poacherType->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Offense Type</th>
                                                        <td>{{ $poacher->offenceType->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Poaching Reason</th>
                                                        <td>{{ $poacher->poachingReason->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Location</th>
                                                        <td>
                                                            @if($poacher->country)
                                                                {{ $poacher->country->name }}
                                                                @if($poacher->province)
                                                                    , {{ $poacher->province->name }}
                                                                    @if($poacher->city)
                                                                        , {{ $poacher->city->name }}
                                                                    @endif
                                                                @endif
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-data">
                            <i class="fas fa-info-circle"></i>
                            <p>No poachers information available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($poachingIncident->latitude && $poachingIncident->longitude)
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Map initialization if the map exists
        const mapElement = document.getElementById('map');
        if (mapElement) {
            // Map configuration - read from data attributes
            const lat = parseFloat(mapElement.dataset.lat);
            const lng = parseFloat(mapElement.dataset.lng);
            const title = mapElement.dataset.title;
            const description = mapElement.dataset.description;
            
            // Initialize the map
            const map = L.map('map').setView([lat, lng], 13);

            // Add the tile layer (map background)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Add zoom control to the top right
            L.control.zoom({
                position: 'topright'
            }).addTo(map);
            
            // Add a scale bar
            L.control.scale({
                imperial: false,
                position: 'bottomleft'
            }).addTo(map);
            
            // Create a custom marker icon
            const customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: '<div style="background-color: rgba(220, 53, 69, 0.8); width: 22px; height: 22px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.3);"></div>',
                iconSize: [22, 22],
                iconAnchor: [11, 11],
                popupAnchor: [0, -11]
            });
            
            // Add a marker with a popup
            const marker = L.marker([lat, lng], {
                icon: customIcon
            }).addTo(map);
            
            // Create a nicer popup
            const popupContent = 
                '<div style="font-family: Arial, sans-serif; padding: 8px;">' +
                '<h5 style="margin: 0 0 8px 0; color: #2d5a27; font-weight: bold;">' + title + '</h5>' +
                '<p style="margin: 0 0 5px 0; color: #555;">' + description + '</p>' +
                '<div style="background-color: #f8f9fa; padding: 5px; border-radius: 4px; margin-top: 8px;">' +
                '<p style="margin: 0; font-size: 12px; color: #666; font-family: monospace; text-align: center;">' +
                '<i class="fas fa-map-marker-alt" style="color: #dc3545;"></i> ' +
                lat.toFixed(6) + ', ' + lng.toFixed(6) +
                '</p>' +
                '</div>' +
                '</div>';
            
            marker.bindPopup(popupContent, {
                maxWidth: 300
            }).openPopup();
            
            // Add a pulsing effect to highlight the location
            const pulsingIcon = L.divIcon({
                className: 'pulse-marker',
                html: '<div class="pulse-marker"></div>',
                iconSize: [0, 0]
            });
            
            // Add the pulsing marker
            L.marker([lat, lng], {
                icon: pulsingIcon,
                zIndexOffset: -1000
            }).addTo(map);
            
            // Add a circle to highlight the area
            L.circle([lat, lng], {
                color: '#2d5a27',
                fillColor: '#2d5a27',
                fillOpacity: 0.1,
                weight: 2,
                dashArray: '5, 5',
                radius: 1000
            }).addTo(map);
        }
    });
</script>
@endif
@endpush 