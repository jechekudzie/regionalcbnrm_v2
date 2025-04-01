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
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #2d5a27 !important;
        border-color: #2d5a27 !important;
        color: white !important;
    }
    #map {
        height: 400px;
        width: 100%;
        border-radius: 0.375rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ isset($wildlifeConflictIncident) ? 'Edit' : 'Record' }} Wildlife Conflict Incident</h5>
                </div>
                <div class="card-body">
                    <form action="{{ isset($wildlifeConflictIncident) ? route('organisation.wildlife-conflicts.update', ['organisation' => $organisation->slug, 'wildlifeConflictIncident' => $wildlifeConflictIncident]) : route('organisation.wildlife-conflicts.store', $organisation->slug) }}" 
                          method="POST" 
                          class="needs-validation" 
                          novalidate>
                        @csrf
                        @if(isset($wildlifeConflictIncident))
                            @method('PATCH')
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $wildlifeConflictIncident->title ?? '') }}" 
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="period" class="form-label">Period (Year)</label>
                                    <select class="form-select @error('period') is-invalid @enderror" 
                                            id="period" 
                                            name="period" 
                                            required>
                                        <option value="">Select Year</option>
                                        @for ($year = date('Y'); $year >= 1900; $year--)
                                            <option value="{{ $year }}" 
                                                    {{ old('period', $wildlifeConflictIncident->period ?? date('Y')) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('period')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="conflict_type_id" class="form-label">Conflict Type</label>
                                    <select class="form-select @error('conflict_type_id') is-invalid @enderror" 
                                            id="conflict_type_id" 
                                            name="conflict_type_id" 
                                            required>
                                        <option value="">Select Type</option>
                                        @foreach($conflictTypes as $type)
                                            <option value="{{ $type->id }}" 
                                                    {{ old('conflict_type_id', $wildlifeConflictIncident->conflict_type_id ?? '') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('conflict_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="incident_date" class="form-label">Incident Date & Time</label>
                                    <input type="text" 
                                           class="form-control datetimepicker @error('incident_date') is-invalid @enderror" 
                                           id="incident_date" 
                                           name="incident_date" 
                                           value="{{ old('incident_date', isset($wildlifeConflictIncident) ? $wildlifeConflictIncident->incident_date->format('Y-m-d H:i') : '') }}" 
                                           required>
                                    @error('incident_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="incident_time" class="form-label">Incident Time</label>
                                    <input type="text" 
                                           class="form-control datetimepicker @error('incident_time') is-invalid @enderror" 
                                           id="incident_time" 
                                           name="incident_time" 
                                           value="{{ old('incident_time', isset($wildlifeConflictIncident) ? $wildlifeConflictIncident->incident_time->format('H:i') : '') }}" 
                                           placeholder="hour : minute"
                                           data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}'
                                           required>
                                    @error('incident_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Species Involved</label>
                                    <div class="row">
                                        @foreach($species as $specie)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input @error('species') is-invalid @enderror" 
                                                           type="checkbox" 
                                                           name="species[]" 
                                                           value="{{ $specie->id }}" 
                                                           id="species_{{ $specie->id }}"
                                                           {{ (old('species') && in_array($specie->id, old('species'))) || 
                                                              (isset($wildlifeConflictIncident) && $wildlifeConflictIncident->species->contains($specie->id)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="species_{{ $specie->id }}">
                                                        {{ $specie->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('species')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="location_description" class="form-label">Location Description</label>
                                    <input type="text" 
                                           class="form-control @error('location_description') is-invalid @enderror" 
                                           id="location_description" 
                                           name="location_description" 
                                           value="{{ old('location_description', $wildlifeConflictIncident->location_description ?? '') }}" 
                                           required>
                                    @error('location_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" 
                                           class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" 
                                           name="latitude" 
                                           value="{{ old('latitude', $wildlifeConflictIncident->latitude ?? '') }}" 
                                           step="any">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" 
                                           class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" 
                                           name="longitude" 
                                           value="{{ old('longitude', $wildlifeConflictIncident->longitude ?? '') }}" 
                                           step="any">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div id="map"></div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4" 
                                              required>{{ old('description', $wildlifeConflictIncident->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($wildlifeConflictIncident) ? 'Update' : 'Record' }} Incident
                                </button>
                                <a href="{{ route('organisation.wildlife-conflicts.index', $organisation->slug) }}" 
                                   class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize datetimepicker
        $('.datetimepicker').datetimepicker({
            format: 'Y-m-d H:i',
            step: 30,
            scrollInput: false
        });

        // Initialize timepicker with data-options
        $('#incident_time').datetimepicker({
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            disableMobile: true,
            step: 30,
            scrollInput: false
        });

        var map = L.map('map').setView([-17.8216, 31.0492], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker = null;
        
        @php
            $lat = old('latitude');
            $lng = old('longitude');
            if (!$lat && !$lng && isset($wildlifeConflictIncident)) {
                $lat = $wildlifeConflictIncident->latitude;
                $lng = $wildlifeConflictIncident->longitude;
            }
        @endphp

        @if($lat && $lng)
            marker = L.marker([{{ $lat }}, {{ $lng }}]).addTo(map);
            map.setView([{{ $lat }}, {{ $lng }}], 13);
        @endif

        map.on('click', function(e) {
            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker(e.latlng).addTo(map);
            document.getElementById('latitude').value = e.latlng.lat;
            document.getElementById('longitude').value = e.latlng.lng;
        });
    });
</script>
@endsection 