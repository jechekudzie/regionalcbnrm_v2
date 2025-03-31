@extends('layouts.organisation')

@section('title', 'Edit Hunting Activity')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Edit Hunting Activity</h2>
                <div>
                    <a href="{{ route('organisation.hunting-activities.show', [$organisation->slug ?? '', $huntingActivity]) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    <a href="{{ route('organisation.hunting-activities.index', $organisation->slug ?? '') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('organisation.hunting-activities.update', [$organisation->slug ?? '', $huntingActivity]) }}" 
                  method="POST" id="huntingActivityForm">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="hunting_concession_id" class="form-label">Hunting Concession</label>
                        <select name="hunting_concession_id" id="hunting_concession_id" class="form-select" required>
                            <option value="">Select Concession</option>
                            @foreach($concessions as $concession)
                                <option value="{{ $concession->id }}" 
                                    {{ old('hunting_concession_id', $huntingActivity->hunting_concession_id) == $concession->id ? 'selected' : '' }}>
                                    {{ $concession->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="safari_id" class="form-label">Safari Operator</label>
                        <select name="safari_id" id="safari_id" class="form-select">
                            <option value="">Select Safari Operator</option>
                            @foreach($safariOperators as $operator)
                                <option value="{{ $operator->id }}" 
                                    {{ old('safari_id', $huntingActivity->safari_id) == $operator->id ? 'selected' : '' }}>
                                    {{ $operator->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ old('start_date', $huntingActivity->start_date->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ old('end_date', $huntingActivity->end_date?->format('Y-m-d')) }}">
                    </div>

                    <div class="col-12">
                        <hr>
                        <h4>Professional Hunter Licenses</h4>
                        <div id="professional_hunter_licenses">
                            @forelse($huntingActivity->professionalHunterLicenses as $index => $license)
                                <div class="row g-3 mb-3 license-row">
                                    <div class="col-md-5">
                                        <label class="form-label">Hunter Name</label>
                                        <input type="text" name="professional_hunter_licenses[{{ $index }}][hunter_name]" 
                                               class="form-control" required value="{{ $license->hunter_name }}">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">License Number</label>
                                        <input type="text" name="professional_hunter_licenses[{{ $index }}][license_number]" 
                                               class="form-control" required value="{{ $license->license_number }}">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        @if($index === 0)
                                            <button type="button" class="btn btn-success add-license">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-danger remove-row">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="row g-3 mb-3 license-row">
                                    <div class="col-md-5">
                                        <label class="form-label">Hunter Name</label>
                                        <input type="text" name="professional_hunter_licenses[0][hunter_name]" 
                                               class="form-control" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">License Number</label>
                                        <input type="text" name="professional_hunter_licenses[0][license_number]" 
                                               class="form-control" required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-success add-license">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="col-12">
                        <hr>
                        <h4>Species Selection</h4>
                        <div id="species_selection">
                            @forelse($huntingActivity->species as $index => $species)
                                <div class="row g-3 mb-3 species-row">
                                    <div class="col-md-6">
                                        <label class="form-label">Species</label>
                                        <select name="species[{{ $index }}][id]" class="form-select species-select" required>
                                            <option value="">Select Species</option>
                                            @foreach($species as $s)
                                                <option value="{{ $s->id }}" 
                                                    {{ $species->id == $s->id ? 'selected' : '' }}>
                                                    {{ $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Off-take</label>
                                        <input type="number" name="species[{{ $index }}][off_take]" class="form-control" 
                                               min="0" required value="{{ $species->pivot->off_take }}">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        @if($index === 0)
                                            <button type="button" class="btn btn-success add-species">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-danger remove-row">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="row g-3 mb-3 species-row">
                                    <div class="col-md-6">
                                        <label class="form-label">Species</label>
                                        <select name="species[0][id]" class="form-select species-select" required>
                                            <option value="">Select Species</option>
                                            @foreach($species as $s)
                                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Off-take</label>
                                        <input type="number" name="species[0][off_take]" class="form-control" 
                                               min="0" required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-success add-species">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="col-12">
                        <hr>
                        <button type="submit" class="btn btn-primary">Update Hunting Activity</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.species-select').select2({
        theme: 'bootstrap-5'
    });

    // Add more licenses
    $('.add-license').click(function() {
        const count = $('.license-row').length;
        const newRow = `
            <div class="row g-3 mb-3 license-row">
                <div class="col-md-5">
                    <label class="form-label">Hunter Name</label>
                    <input type="text" name="professional_hunter_licenses[${count}][hunter_name]" 
                           class="form-control" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">License Number</label>
                    <input type="text" name="professional_hunter_licenses[${count}][license_number]" 
                           class="form-control" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-row">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
        `;
        $('#professional_hunter_licenses').append(newRow);
    });

    // Add more species
    $('.add-species').click(function() {
        const count = $('.species-row').length;
        const newRow = `
            <div class="row g-3 mb-3 species-row">
                <div class="col-md-6">
                    <label class="form-label">Species</label>
                    <select name="species[${count}][id]" class="form-select species-select" required>
                        <option value="">Select Species</option>
                        @foreach($species as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Off-take</label>
                    <input type="number" name="species[${count}][off_take]" class="form-control" 
                           min="0" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-row">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
        `;
        const $newRow = $(newRow);
        $('#species_selection').append($newRow);
        $newRow.find('.species-select').select2({
            theme: 'bootstrap-5'
        });
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.row').remove();
    });

    // Form validation
    $('#huntingActivityForm').submit(function(e) {
        const $form = $(this);
        const $speciesSelects = $form.find('.species-select');
        const selectedSpecies = new Set();

        let hasDuplicates = false;
        $speciesSelects.each(function() {
            const value = $(this).val();
            if (value && selectedSpecies.has(value)) {
                hasDuplicates = true;
                return false;
            }
            selectedSpecies.add(value);
        });

        if (hasDuplicates) {
            e.preventDefault();
            alert('Each species can only be selected once.');
        }
    });
});
</script>
@endpush 