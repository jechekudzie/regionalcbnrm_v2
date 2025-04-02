@extends('layouts.organisation')

@section('title', 'Add Problem Animal Control Record')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
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
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    .form-text {
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #6c757d;
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
    .form-check-input:checked {
        background-color: #2d5a27;
        border-color: #2d5a27;
    }
    .control-measures-box {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .control-measures-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    .conflict-info-box {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #2d5a27;
    }
    .no-conflict-selected {
        color: #6c757d;
        font-style: italic;
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
    .form-floating>.form-control, 
    .form-floating>.form-select {
        height: calc(3.5rem + 2px);
        padding: 1rem 0.75rem;
    }
    .form-floating>label {
        padding: 1rem 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="h3 mb-0">Add Problem Animal Control Record</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard.index', $organisation->slug) }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('organisation.problem-animal-controls.index', $organisation->slug) }}">Problem Animal Control</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add New Record</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <i class="fas fa-shield-alt me-1"></i>
                Problem Animal Control Details
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('organisation.problem-animal-controls.store', $organisation->slug) }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="wildlife_conflict_incident_id" class="form-label">Related Wildlife Conflict Incident <span class="text-danger">*</span></label>
                        <select name="wildlife_conflict_incident_id" id="wildlife_conflict_incident_id" class="form-select select2 @error('wildlife_conflict_incident_id') is-invalid @enderror">
                            <option value="">-- Select Wildlife Conflict Incident --</option>
                            @foreach($wildlifeConflictIncidents as $incident)
                                <option value="{{ $incident->id }}" 
                                    {{ (old('wildlife_conflict_incident_id') == $incident->id || (isset($wildlifeConflictIncident) && $wildlifeConflictIncident->id == $incident->id)) ? 'selected' : '' }}
                                    data-species="{{ $incident->species->pluck('name')->join(', ') }}"
                                    data-location="{{ $incident->location_description }}"
                                    data-latitude="{{ $incident->latitude }}"
                                    data-longitude="{{ $incident->longitude }}"
                                    data-date="{{ $incident->incident_date->format('Y-m-d') }}"
                                    data-period="{{ $incident->period }}">
                                    {{ $incident->title }} ({{ $incident->incident_date->format('d M Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('wildlife_conflict_incident_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Select the wildlife conflict incident this control measure relates to.</div>
                    </div>
                </div>

                <div class="conflict-info-box mb-3" id="conflict-info-box">
                    <h6 class="mb-2"><i class="fas fa-info-circle me-1"></i> Selected Conflict Information</h6>
                    <div id="conflict-info-content">
                        <p class="no-conflict-selected">No conflict incident selected yet. Please select a wildlife conflict incident above.</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="control_date" class="form-label">Control Date <span class="text-danger">*</span></label>
                        <input type="text" class="form-control datepicker @error('control_date') is-invalid @enderror" id="control_date" name="control_date" value="{{ old('control_date') }}" placeholder="YYYY-MM-DD">
                        @error('control_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="control_time" class="form-label">Control Time</label>
                        <input type="text" class="form-control timepicker @error('control_time') is-invalid @enderror" id="control_time" name="control_time" value="{{ old('control_time') }}" placeholder="HH:MM">
                        @error('control_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="period" class="form-label">Period (Year) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('period') is-invalid @enderror" id="period" name="period" value="{{ old('period', date('Y')) }}" min="1900" max="{{ date('Y') }}">
                        @error('period')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}" placeholder="Enter location">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="text" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="Enter latitude">
                        @error('latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Decimal format e.g. -15.4128</div>
                    </div>
                    <div class="col-md-3">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="text" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="Enter longitude">
                        @error('longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Decimal format e.g. 28.2891</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="estimated_number" class="form-label">Estimated Number of Animals <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('estimated_number') is-invalid @enderror" id="estimated_number" name="estimated_number" value="{{ old('estimated_number', 1) }}" min="1">
                        @error('estimated_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Estimated number of animals affected by this control measure.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Enter additional details about the control activities">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Control Measures <span class="text-danger">*</span></label>
                        <div class="control-measures-box">
                            <div class="form-text mb-2">Select the control measures that were implemented:</div>
                            <div class="control-measures-list">
                                @foreach($controlMeasures as $measure)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="control_measures[]" value="{{ $measure->id }}" id="measure_{{ $measure->id }}" {{ in_array($measure->id, old('control_measures', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="measure_{{ $measure->id }}">
                                            {{ $measure->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('control_measures')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('organisation.problem-animal-controls.index', $organisation->slug) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Initialize Flatpickr for date and time pickers
        $(".datepicker").flatpickr({
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            maxDate: "today"
        });

        $(".timepicker").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });

        // Handle wildlife conflict incident selection
        $('#wildlife_conflict_incident_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const conflictInfoBox = $('#conflict-info-content');
            
            if ($(this).val()) {
                // Get data attributes
                const species = selectedOption.data('species');
                const location = selectedOption.data('location');
                const latitude = selectedOption.data('latitude');
                const longitude = selectedOption.data('longitude');
                const date = selectedOption.data('date');
                const period = selectedOption.data('period');
                
                // Create HTML content
                let html = '<div class="row">';
                html += '<div class="col-md-6">';
                html += '<p class="mb-1"><strong>Location:</strong> ' + location + '</p>';
                if (latitude && longitude) {
                    html += '<p class="mb-1"><strong>Coordinates:</strong> ' + latitude + ', ' + longitude + '</p>';
                }
                html += '<p class="mb-1"><strong>Date:</strong> ' + date + ' (' + period + ')</p>';
                html += '</div>';
                html += '<div class="col-md-6">';
                html += '<p class="mb-1"><strong>Species:</strong></p>';
                if (species) {
                    const speciesArray = species.split(',');
                    html += '<div>';
                    speciesArray.forEach(function(item) {
                        html += '<span class="species-badge">' + item.trim() + '</span>';
                    });
                    html += '</div>';
                } else {
                    html += '<p>No species specified</p>';
                }
                html += '</div>';
                html += '</div>';
                
                // Update the conflict info box
                conflictInfoBox.html(html);
                
                // Pre-fill the form fields with conflict data
                $('#location').val(location);
                $('#latitude').val(latitude);
                $('#longitude').val(longitude);
                $('#period').val(period);
            } else {
                // Clear the conflict info box if nothing is selected
                conflictInfoBox.html('<p class="no-conflict-selected">No conflict incident selected yet. Please select a wildlife conflict incident above.</p>');
                
                // Reset the form fields
                $('#location').val('');
                $('#latitude').val('');
                $('#longitude').val('');
                $('#period').val(new Date().getFullYear());
            }
        });
        
        // Trigger change event to update the form on page load if a conflict is pre-selected
        $('#wildlife_conflict_incident_id').trigger('change');
    });
</script>
@endpush 