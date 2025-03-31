@extends('layouts.organisation')

@section('title', 'Hunting Concession Details')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        .card-header {
            background-color: #2e7d32;
            color: white;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }
        .detail-table tr {
            border-color: rgba(46, 125, 50, 0.1);
        }
        .detail-table th {
            width: 200px;
            border-right: 1px solid rgba(46, 125, 50, 0.1);
            vertical-align: middle;
        }
        .icon-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(46, 125, 50, 0.1);
            color: #2e7d32;
            margin-right: 10px;
        }
        .location-badge {
            background-color: rgba(46, 125, 50, 0.1);
            color: #2e7d32;
            padding: 0.35rem 0.65rem;
            border-radius: 16px;
            font-size: 0.9rem;
            display: inline-block;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-nature-green shadow-sm" style="background-color: #2e7d32 !important;">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-0 text-white" style="color: white !important;">{{ $huntingConcession->name }}</h3>
                            <p class="mb-0 opacity-75 text-white" style="color: white !important;">
                                <i class="fas fa-map-marked-alt me-1"></i> Hunting Concession Details
                                @if($huntingConcession->hectarage)
                                    <span class="ms-3 badge bg-warning text-dark">{{ $huntingConcession->hectarage }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('organisation.hunting-concessions.edit', [$organisation->slug ?? '', $huntingConcession]) }}" 
                               class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Concession
                            </a>
                            <a href="{{ route('organisation.hunting-concessions.index', $organisation->slug ?? '') }}" 
                               class="btn btn-outline-warning">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Concession Details Card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header" style="background-color: #2e7d32 !important; color: white !important;">
                    <div class="d-flex align-items-center">
                        <span class="icon-circle bg-white" style="background-color: white !important;">
                            <i class="fas fa-info" style="color: #2e7d32 !important;"></i>
                        </span>
                        <h5 class="mb-0 fw-bold" style="color: white !important;">Concession Information</h5>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table detail-table">
                        <tr>
                            <th class="detail-label">Name:</th>
                            <td class="fw-medium">{{ $huntingConcession->name }}</td>
                        </tr>
                        <tr>
                            <th class="detail-label">Hectarage:</th>
                            <td>
                                @if($huntingConcession->hectarage)
                                    <span class="fw-medium">{{ $huntingConcession->hectarage }}</span>
                                @else
                                    <span class="text-muted fst-italic">Not specified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="detail-label">Safari Operator:</th>
                            <td>
                                @if($huntingConcession->safariOperator)
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle-sm me-2" style="width: 28px; height: 28px; border-radius: 50%; background-color: rgba(46, 125, 50, 0.1); display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user-tie" style="color: #2e7d32;"></i>
                                        </div>
                                        <span class="fw-medium">{{ $huntingConcession->safariOperator->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">Not assigned</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="detail-label">Location:</th>
                            <td>
                                @if($huntingConcession->latitude && $huntingConcession->longitude)
                                    <span class="location-badge">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $huntingConcession->latitude }}, {{ $huntingConcession->longitude }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">Location not specified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="detail-label">Created At:</th>
                            <td>{{ $huntingConcession->created_at->format('M d, Y H:i A') }}</td>
                        </tr>
                        <tr>
                            <th class="detail-label">Last Updated:</th>
                            <td>{{ $huntingConcession->updated_at->format('M d, Y H:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Map Card -->
        @if($huntingConcession->latitude && $huntingConcession->longitude)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header" style="background-color: #2e7d32 !important; color: white !important;">
                        <div class="d-flex align-items-center">
                            <span class="icon-circle bg-white" style="background-color: white !important;">
                                <i class="fas fa-map-marker-alt" style="color: #2e7d32 !important;"></i>
                            </span>
                            <h5 class="mb-0 fw-bold" style="color: white !important;">Location Map</h5>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="map" style="height: 400px; border-radius: 0 0 0.25rem 0.25rem;"></div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header" style="background-color: #2e7d32 !important; color: white !important;">
                        <div class="d-flex align-items-center">
                            <span class="icon-circle bg-white" style="background-color: white !important;">
                                <i class="fas fa-map" style="color: #2e7d32 !important;"></i>
                            </span>
                            <h5 class="mb-0 fw-bold" style="color: white !important;">Location Map</h5>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-map-marker-alt fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No Location Data Available</h5>
                            <p class="text-muted mb-0">
                                Edit this concession to add location coordinates.
                            </p>
                            <a href="{{ route('organisation.hunting-concessions.edit', [$organisation->slug ?? '', $huntingConcession]) }}" 
                               class="btn btn-outline-success mt-3">
                                <i class="fas fa-edit me-1"></i> Add Location
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@if($huntingConcession->latitude && $huntingConcession->longitude)
    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Map configuration
            var mapConfig = {
                lat: {!! json_encode($huntingConcession->latitude) !!},
                lng: {!! json_encode($huntingConcession->longitude) !!},
                name: {!! json_encode($huntingConcession->name) !!},
                hectarage: {!! json_encode($huntingConcession->hectarage ?? 'Hectarage not specified') !!}
            };
            
            // Initialize the map
            var map = L.map('map').setView([mapConfig.lat, mapConfig.lng], 13);

            // Add the tile layer (map background)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add the marker with a popup
            var marker = L.marker([mapConfig.lat, mapConfig.lng]).addTo(map);
            marker.bindPopup("<strong>" + mapConfig.name + "</strong><br>" + mapConfig.hectarage).openPopup();
            
            // Add a circle to highlight the area
            L.circle([mapConfig.lat, mapConfig.lng], {
                color: '#2e7d32',
                fillColor: '#2e7d32',
                fillOpacity: 0.2,
                radius: 1000
            }).addTo(map);
        });
    </script>
    @endpush
@endif
@endsection
