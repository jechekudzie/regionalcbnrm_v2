@extends('layouts.organisation')

@section('title', 'Hunting Concession Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Hunting Concession Details</h5>
                        <div>
                            <a href="{{ route('organisation.hunting-concessions.edit', [$organisation->slug ?? '', $huntingConcession]) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Concession
                            </a>
                            <a href="{{ route('organisation.hunting-concessions.index', $organisation->slug ?? '') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th style="width: 200px;">Name:</th>
                                    <td>{{ $huntingConcession->name }}</td>
                                </tr>
                                <tr>
                                    <th>Hectarage:</th>
                                    <td>{{ $huntingConcession->hectarage ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Location:</th>
                                    <td>
                                        @if($huntingConcession->latitude && $huntingConcession->longitude)
                                            {{ $huntingConcession->latitude }}, {{ $huntingConcession->longitude }}
                                        @else
                                            Location not specified
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $huntingConcession->created_at->format('M d, Y H:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $huntingConcession->updated_at->format('M d, Y H:i A') }}</td>
                                </tr>
                            </table>

                            @if($huntingConcession->description)
                                <div class="mt-4">
                                    <h6 class="mb-3">Description</h6>
                                    <p class="text-muted">{{ $huntingConcession->description }}</p>
                                </div>
                            @endif
                        </div>

                        @if($huntingConcession->latitude && $huntingConcession->longitude)
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Location Map</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="map" style="height: 400px;"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($huntingConcession->latitude && $huntingConcession->longitude)
    @push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map').setView([
                {{ $huntingConcession->latitude }}, 
                {{ $huntingConcession->longitude }}
            ], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            L.marker([
                {{ $huntingConcession->latitude }}, 
                {{ $huntingConcession->longitude }}
            ]).addTo(map)
            .bindPopup("{{ $huntingConcession->name }}")
            .openPopup();
        });
    </script>
    @endpush
@endif
@endsection
