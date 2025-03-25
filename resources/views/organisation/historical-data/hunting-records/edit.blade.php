@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Edit Hunting Record
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2 text-primary"></i>Edit Quota Allocation and Utilisation Record
                </h5>
                <p class="mb-0 text-muted small">Update historical hunting quota and utilization data</p>
            </div>
            <div class="col-auto align-self-center">
                <a href="{{ route('hunting_records.index', $organisation->slug) }}" class="btn btn-outline-primary">
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

        <form action="{{ route('hunting_records.update', [$organisation->slug, $huntingRecord]) }}" method="POST">
            @csrf
            @method('PUT')
            
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
                            <option value="{{ $year }}" {{ old('period', $huntingRecord->period) == $year ? 'selected' : '' }}>{{ $year }}</option>
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
                            <option value="{{ $specie->id }}" {{ old('species_id', $huntingRecord->species_id) == $specie->id ? 'selected' : '' }}>
                                {{ $specie->name }} ({{ $specie->scientific_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('species_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Allocated -->
                <div class="col-md-6">
                    <label class="form-label" for="allocated">Quota Allocated</label>
                    <input type="number" 
                           class="form-control @error('allocated') is-invalid @enderror" 
                           id="allocated" 
                           name="allocated" 
                           value="{{ old('allocated', $huntingRecord->allocated) }}"
                           min="0"
                           required>
                    @error('allocated')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Utilised -->
                <div class="col-md-6">
                    <label class="form-label" for="utilised">Animals Hunted/Utilised</label>
                    <input type="number" 
                           class="form-control @error('utilised') is-invalid @enderror" 
                           id="utilised" 
                           name="utilised" 
                           value="{{ old('utilised', $huntingRecord->utilised) }}"
                           min="0"
                           required>
                    @error('utilised')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Update Record
                </button>
                <a href="{{ route('hunting_records.index', $organisation->slug) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 