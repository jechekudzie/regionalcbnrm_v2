@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Edit Crop Conflict Record
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2 text-primary"></i>Edit Crop Conflict Record
                </h5>
                <p class="mb-0 text-muted small">Update historical crop damage data from wildlife conflicts</p>
            </div>
            <div class="col-auto align-self-center">
                <a href="{{ route('crop_conflict_records.index', $organisation->slug) }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Records
                </a>
            </div>
        </div>
    </div>
    <div class="card-body bg-light">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible mb-4">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <h6 class="mb-0">Please correct the following errors:</h6>
                </div>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('crop_conflict_records.update', [$organisation->slug, $cropConflict]) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="row g-3">
                <!-- Year/Period -->
                <div class="col-md-6">
                    <label class="form-label" for="period">Year</label>
                    <select class="form-select @error('period') is-invalid @enderror" 
                            id="period" 
                            name="period" 
                            required>
                        <option value="">Select Year</option>
                        @for ($year = date('Y'); $year >= 1990; $year--)
                            <option value="{{ $year }}" {{ old('period', $cropConflict->period) == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                    @error('period')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Species -->
                <div class="col-md-6">
                    <label class="form-label" for="species_id">Species</label>
                    <select class="form-select @error('species_id') is-invalid @enderror" 
                            id="species_id" 
                            name="species_id" 
                            required>
                        <option value="">Select Species</option>
                        @foreach($species as $specie)
                            <option value="{{ $specie->id }}" {{ old('species_id', $cropConflict->species_id) == $specie->id ? 'selected' : '' }}>
                                {{ $specie->name }} ({{ $specie->scientific_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('species_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Crop Type -->
                <div class="col-md-6">
                    <label class="form-label" for="crop_type_id">Crop Type</label>
                    <select class="form-select @error('crop_type_id') is-invalid @enderror" 
                            id="crop_type_id" 
                            name="crop_type_id" 
                            required>
                        <option value="">Select Crop Type</option>
                        @foreach($cropTypes as $cropType)
                            <option value="{{ $cropType->id }}" {{ old('crop_type_id', $cropConflict->crop_type_id) == $cropType->id ? 'selected' : '' }}>
                                {{ $cropType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('crop_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Hectares Destroyed -->
                <div class="col-md-6">
                    <label class="form-label" for="hectrage_destroyed">Hectares Destroyed</label>
                    <input type="number" 
                           class="form-control @error('hectrage_destroyed') is-invalid @enderror" 
                           id="hectrage_destroyed" 
                           name="hectrage_destroyed" 
                           value="{{ old('hectrage_destroyed', $cropConflict->hectrage_destroyed) }}"
                           min="0"
                           step="0.01"
                           required>
                    @error('hectrage_destroyed')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Update Record
                </button>
                <a href="{{ route('crop_conflict_records.index', $organisation->slug) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 