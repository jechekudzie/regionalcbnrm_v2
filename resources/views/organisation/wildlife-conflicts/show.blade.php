@extends('layouts.organisation')

@section('title', $wildlifeConflictIncident->title)

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        /* Modern styling for outcome cards */
        .list-group-item {
            transition: all 0.3s ease;
            border-width: 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .list-group-item:last-child {
            border-bottom: none;
        }
        
        .badge.bg-success {
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0,128,0,0.2);
        }
        
        .btn-group .btn {
            border-radius: 4px;
            margin: 0 2px;
            transition: all 0.2s;
        }
        
        .btn-group .btn:hover {
            transform: translateY(-2px);
        }
        
        .card {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .card-header.bg-success {
            background: linear-gradient(45deg, #2E7D32, #43A047) !important;
        }
        
        .btn-success {
            background: linear-gradient(45deg, #2E7D32, #43A047);
            border-color: #2E7D32;
        }
        
        .text-success {
            color: #2E7D32 !important;
        }
        
        /* Animations for field values */
        .col .d-flex {
            transition: all 0.2s ease;
        }
        
        .col .d-flex:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            background-color: rgba(46, 125, 50, 0.05);
            border-radius: 4px;
            padding: 0.5rem;
            margin: -0.5rem;
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
            background-color: #2E7D32;
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
            background: rgba(46, 125, 50, 0.2);
            animation: pulse 2s infinite;
            pointer-events: none;
        }
        
        /* Problem Animal Control card styling */
        .pac-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 16px;
            transition: all 0.3s ease;
            border-left: 4px solid #2E7D32;
            overflow: hidden;
            position: relative;
        }
        
        .pac-card:hover {
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
            transform: translateY(-3px);
        }
        
        .pac-card:hover::before {
            opacity: 1;
        }
        
        .pac-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100%;
            background: linear-gradient(90deg, rgba(46,125,50,0) 0%, rgba(46,125,50,0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .pac-card .card-body {
            padding: 18px;
            background-color: #fcfcfc;
        }
        
        .pac-date {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
        }
        
        .pac-date::before {
            content: '\f073';
            font-family: 'Font Awesome 5 Free';
            margin-right: 8px;
            color: #2E7D32;
            font-size: 0.9rem;
        }
        
        .pac-location {
            color: #666;
            margin-bottom: 8px;
            font-size: 0.85rem;
            padding-left: 24px;
        }
        
        .pac-badge-animals {
            background: linear-gradient(135deg, #2E7D32, #43A047);
            font-weight: 500;
            font-size: 0.85rem;
            padding: 0.35em 0.75em;
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(46,125,50,0.2);
            color: white;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .pac-badge-animals::before {
            content: '\f1b0';
            font-family: 'Font Awesome 5 Free';
            margin-right: 6px;
            font-weight: 900;
        }
        
        .pac-badge-animals:hover {
            transform: scale(1.05);
            box-shadow: 0 3px 7px rgba(46,125,50,0.3);
        }
        
        .pac-measures {
            margin-top: 14px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            border-left: 3px solid #e9ecef;
        }
        
        .pac-measures-title {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .pac-measures-title::before {
            content: '\f0e7';
            font-family: 'Font Awesome 5 Free';
            margin-right: 6px;
            font-weight: 900;
            color: #ffc107;
            font-size: 0.85rem;
        }
        
        .pac-measure-badge {
            background-color: white;
            color: #333;
            font-size: 0.8rem;
            padding: 0.35em 0.75em;
            border-radius: 15px;
            margin-right: 6px;
            margin-bottom: 6px;
            display: inline-block;
            border: 1px solid #e9ecef;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }
        
        .pac-measure-badge:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            transform: translateY(-1px);
        }
        
        .pac-details-btn {
            font-size: 0.85rem;
            border-radius: 20px;
            padding: 0.35em 1.2em;
            background-color: white;
            color: #2E7D32;
            border: 1px solid #2E7D32;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .pac-details-btn:hover {
            background-color: #2E7D32;
            color: white;
            box-shadow: 0 3px 6px rgba(46,125,50,0.2);
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4 border-bottom pb-2">
        <div class="row">
            <div class="col-md-8">
                <h1>{{ $wildlifeConflictIncident->title }}</h1>
                <p class="text-muted">
                    <i class="fas fa-calendar-alt"></i> {{ $wildlifeConflictIncident->incident_date->format('d M Y') }} at {{ $wildlifeConflictIncident->incident_time->format('H:i') }}
                    <span class="ms-3"><i class="fas fa-clock"></i> {{ $wildlifeConflictIncident->period }}</span>
                    <span class="ms-3 badge bg-success">{{ $wildlifeConflictIncident->conflictType->name }}</span>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('organisation.wildlife-conflicts.edit', [$organisation->slug, $wildlifeConflictIncident->id]) }}" class="btn btn-success">
                    <i class="fas fa-edit"></i> Edit Incident
                </a>
                <a href="{{ route('organisation.wildlife-conflicts.index', $organisation->slug) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Incident Details -->
            <div class="card mb-4 border">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Incident Details</h5>
                </div>
                <div class="card-body">
                    <dl>
                        <dt class="fw-bold text-muted">Conflict Type:</dt>
                        <dd class="mb-3">{{ $wildlifeConflictIncident->conflictType->name }}</dd>
                        
                        <dt class="fw-bold text-muted">Date & Time:</dt>
                        <dd class="mb-3">{{ $wildlifeConflictIncident->incident_date->format('d M Y') }} at {{ $wildlifeConflictIncident->incident_time->format('H:i') }}</dd>
                        
                        <dt class="fw-bold text-muted">Period:</dt>
                        <dd class="mb-3">{{ $wildlifeConflictIncident->period }}</dd>
                        
                        <dt class="fw-bold text-muted">Species Involved:</dt>
                        <dd class="mb-3">
                            @forelse($wildlifeConflictIncident->species as $species)
                                <span class="badge bg-success me-1">{{ $species->name }}</span>
                            @empty
                                <span class="text-muted">No species recorded</span>
                            @endforelse
                        </dd>
                        
                        <dt class="fw-bold text-muted">Location Description:</dt>
                        <dd class="mb-3">{{ $wildlifeConflictIncident->location_description }}</dd>
                        
                        @if($wildlifeConflictIncident->latitude && $wildlifeConflictIncident->longitude)
                        <dt class="fw-bold text-muted">Coordinates:</dt>
                        <dd class="mb-3">
                            <span class="bg-light px-2 py-1 rounded font-monospace">
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                {{ number_format($wildlifeConflictIncident->latitude, 6) }}, {{ number_format($wildlifeConflictIncident->longitude, 6) }}
                            </span>
                        </dd>
                        @endif
                    </dl>
                </div>
            </div>
            
            <!-- Incident Description -->
            <div class="card mb-4 border">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-align-left me-2"></i>Incident Description</h5>
                </div>
                <div class="card-body">
                    <p>{{ $wildlifeConflictIncident->description }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <!-- Location Map -->
            <div class="card mb-4 border">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-map-marked-alt me-2"></i>Location Map</h5>
                </div>
                <div class="card-body p-0">
                    @if($wildlifeConflictIncident->latitude && $wildlifeConflictIncident->longitude)
                        <div id="map" 
                             data-lat="{{ $wildlifeConflictIncident->latitude }}"
                             data-lng="{{ $wildlifeConflictIncident->longitude }}"
                             data-title="{{ $wildlifeConflictIncident->title }}"
                             data-description="{{ $wildlifeConflictIncident->location_description }}"
                             style="height: 500px; width: 100%; border-radius: 0 0 4px 4px;"></div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                            <p>No location data available for this incident</p>
                            <a href="{{ route('organisation.wildlife-conflicts.edit', [$organisation->slug, $wildlifeConflictIncident->id]) }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Add Location Data
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Problem Animal Control Card -->
            <div class="card mb-4 border">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-shield-alt me-2"></i>Problem Animal Control</h5>
                    <a href="{{ route('organisation.problem-animal-controls.create', [$organisation->slug, 'wildlife_conflict_incident_id' => $wildlifeConflictIncident->id]) }}" 
                       class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Add New Record
                    </a>
                </div>
                <div class="card-body">
                    @if($wildlifeConflictIncident->problemAnimalControls->count() > 0)
                        <div class="px-2">
                            @foreach($wildlifeConflictIncident->problemAnimalControls as $control)
                                <div class="card border-0 shadow mb-4" style="border-radius: 15px; overflow: hidden;">
                                    <div class="row g-0">
                                        <!-- Left side date and animals count -->
                                        <div class="col-md-3 bg-success text-white p-3 d-flex flex-column justify-content-center align-items-center" style="background: linear-gradient(45deg, #1b5e20, #2e7d32);">
                                            <h3 class="fw-bold mb-0">{{ $control->control_date->format('d') }}</h3>
                                            <div class="mb-2">{{ $control->control_date->format('M Y') }}</div>
                                            <div class="border-top border-bottom border-white-50 w-100 my-2 py-2 text-center">
                                                <span class="badge bg-white text-success p-2 rounded-pill">
                                                    <i class="fas fa-paw me-1"></i> {{ $control->estimated_number }} Animals
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Right side details -->
                                        <div class="col-md-9">
                                            <div class="card-body">
                                                <!-- Location with icon -->
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-light p-2 rounded-circle me-3">
                                                        <i class="fas fa-map-marker-alt text-success"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-muted small">LOCATION</h6>
                                                        <p class="mb-0 fw-medium">{{ $control->location }}</p>
                                                    </div>
                                                </div>
                                                
                                                @if($control->controlMeasures->count() > 0)
                                                    <!-- Control measures -->
                                                    <div class="d-flex align-items-start mb-3">
                                                        <div class="bg-light p-2 rounded-circle me-3">
                                                            <i class="fas fa-bolt text-warning"></i>
                                                        </div>
                                                        <div class="w-100">
                                                            <h6 class="mb-2 text-muted small">CONTROL MEASURES</h6>
                                                            <div>
                                                                @foreach($control->controlMeasures->take(3) as $measure)
                                                                    <span class="badge bg-light text-dark me-1 mb-1 px-3 py-2 shadow-sm" style="border-radius: 15px;">
                                                                        {{ $measure->name }}
                                                                    </span>
                                                                @endforeach
                                                                @if($control->controlMeasures->count() > 3)
                                                                    <span class="badge bg-secondary px-3 py-2" style="border-radius: 15px;">
                                                                        +{{ $control->controlMeasures->count() - 3 }} more
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <!-- View details button -->
                                                <div class="text-end mt-3">
                                                    <a href="{{ route('organisation.problem-animal-controls.show', [$organisation->slug, $control]) }}" 
                                                       class="btn btn-sm px-4 py-2" 
                                                       style="background-color: #2e7d32; color: white; border-radius: 50px; box-shadow: 0 3px 6px rgba(0,0,0,0.16);">
                                                        <i class="fas fa-eye me-1"></i> View Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 bg-light rounded">
                            <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                            <p>No problem animal control records have been created for this incident yet.</p>
                            <a href="{{ route('organisation.problem-animal-controls.create', [$organisation->slug, 'wildlife_conflict_incident_id' => $wildlifeConflictIncident->id]) }}" 
                               class="btn btn-success px-4 py-2" style="border-radius: 50px;">
                                <i class="fas fa-plus me-1"></i> Add Problem Animal Control Record
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Conflict Outcomes Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-clipboard-list me-2"></i>Conflict Outcomes</h5>
                    <a href="{{ route('organisation.wildlife-conflicts.outcomes.create', [$organisation->slug, $wildlifeConflictIncident->id]) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Outcome
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($wildlifeConflictIncident->outcomes->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($wildlifeConflictIncident->outcomes as $outcome)
                                <div class="list-group-item p-0">
                                    <div class="row g-0 {{ $loop->even ? 'bg-light' : '' }}">
                                        <!-- Outcome Type Column -->
                                        <div class="col-md-2 p-3 border-end">
                                            <div class="d-flex flex-column align-items-center text-center">
                                                <span class="badge bg-success mb-2 px-3 py-2">{{ $outcome->conflictOutCome->name }}</span>
                                                <div class="btn-group btn-group-sm mt-2">
                                                    <a href="{{ route('organisation.wildlife-conflicts.outcomes.show', [$organisation->slug, $wildlifeConflictIncident->id, $outcome->id]) }}" 
                                                       class="btn btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('organisation.wildlife-conflicts.outcomes.edit', [$organisation->slug, $wildlifeConflictIncident->id, $outcome->id]) }}" 
                                                       class="btn btn-outline-warning" title="Edit Outcome">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" title="Delete Outcome" 
                                                            onclick="if(confirm('Are you sure you want to delete this outcome?')) { document.getElementById('delete-form-{{ $outcome->id }}').submit(); }">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $outcome->id }}" 
                                                          action="{{ route('organisation.wildlife-conflicts.outcomes.destroy', [$organisation->slug, $wildlifeConflictIncident->id, $outcome->id]) }}" 
                                                          method="POST" class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Details Column -->
                                        <div class="col-md-10 p-3">
                                            @php
                                                $dynamicValues = $outcome->dynamicValues;
                                            @endphp
                                            
                                            @if($dynamicValues->count() > 0)
                                                <div class="row row-cols-1 row-cols-md-3 g-3">
                                                    @foreach($dynamicValues as $value)
                                                        <div class="col">
                                                            <div class="d-flex flex-column">
                                                                <span class="text-muted small">{{ $value->dynamicField->label }}:</span>
                                                                <span class="fw-semibold">{{ Str::limit($value->field_value, 50) }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                @if($dynamicValues->count() > 6)
                                                    <div class="mt-2 text-center">
                                                        <a href="{{ route('organisation.wildlife-conflicts.outcomes.show', [$organisation->slug, $wildlifeConflictIncident->id, $outcome->id]) }}" 
                                                           class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-list me-1"></i> View All Details
                                                        </a>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-muted fst-italic">No details recorded for this outcome</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                            <p>No outcomes have been recorded for this incident yet</p>
                            <a href="{{ route('organisation.wildlife-conflicts.outcomes.create', [$organisation->slug, $wildlifeConflictIncident->id]) }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Add First Outcome
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if($wildlifeConflictIncident->latitude && $wildlifeConflictIncident->longitude)
@push('scripts')
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
                '<h5 style="margin: 0 0 8px 0; color: #2E7D33; font-weight: bold;">' + title + '</h5>' +
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
                color: '#2E7D33',
                fillColor: '#2E7D33',
                fillOpacity: 0.1,
                weight: 2,
                dashArray: '5, 5',
                radius: 1000
            }).addTo(map);
        }
        
        // Outcomes section enhancements
        const outcomeItems = document.querySelectorAll('.list-group-item');
        outcomeItems.forEach(item => {
            // Add hover effect
            item.addEventListener('mouseenter', function() {
                this.style.transition = 'all 0.3s ease';
                this.style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';
                this.style.zIndex = '1';
                
                // Highlight the badge
                const badge = this.querySelector('.badge.bg-success');
                if (badge) {
                    badge.style.transition = 'all 0.3s ease';
                    badge.style.transform = 'scale(1.1)';
                }
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.boxShadow = 'none';
                this.style.zIndex = '0';
                
                // Reset badge
                const badge = this.querySelector('.badge.bg-success');
                if (badge) {
                    badge.style.transform = 'scale(1)';
                }
            });
            
            // Make the entire row clickable to view details
            const detailsLink = item.querySelector('a[title="View Details"]');
            if (detailsLink) {
                const detailsUrl = detailsLink.getAttribute('href');
                const detailsColumn = item.querySelector('.col-md-10');
                
                if (detailsColumn) {
                    detailsColumn.style.cursor = 'pointer';
                    detailsColumn.addEventListener('click', function(e) {
                        // Don't trigger when clicking on buttons
                        if (!e.target.closest('.btn')) {
                            window.location.href = detailsUrl;
                        }
                    });
                }
            }
        });
    });
</script>
@endpush
@endif
