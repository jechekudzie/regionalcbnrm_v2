@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Edit Animal Control Record
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-paw me-1 text-primary"></i>Edit Animal Control Record
                </h5>
                <p class="mb-0 text-muted small">Update historical animal control operations</p>
            </div>
            <div>
                <a href="{{ route('animal_control_records.index', $organisation->slug) }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Records
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex">
                    <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                    <div>
                        <strong>Error!</strong> Please check the form for errors.
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('animal_control_records.update', [$organisation->slug, $animalControl]) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="period" class="form-label">Year <span class="text-danger">*</span></label>
                        <select name="period" id="period" class="form-select @error('period') is-invalid @enderror" required>
                            <option value="">Select Year</option>
                            @for ($year = date('Y'); $year >= 1990; $year--)
                                <option value="{{ $year }}" {{ old('period', $animalControl->period) == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                        @error('period')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="species_id" class="form-label">Species <span class="text-danger">*</span></label>
                        <select name="species_id" id="species_id" class="form-select @error('species_id') is-invalid @enderror" required>
                            <option value="">Select Species</option>
                            @foreach ($species as $specie)
                                <option value="{{ $specie->id }}" {{ old('species_id', $animalControl->species_id) == $specie->id ? 'selected' : '' }}>
                                    {{ $specie->name }} ({{ $specie->scientific_name }})
                                </option>
                            @endforeach
                        </select>
                        @error('species_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="number_of_cases" class="form-label">Number of Cases <span class="text-danger">*</span></label>
                        <input type="number" name="number_of_cases" id="number_of_cases" class="form-control @error('number_of_cases') is-invalid @enderror" 
                            value="{{ old('number_of_cases', $animalControl->number_of_cases) }}" min="0" required>
                        @error('number_of_cases')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="killed" class="form-label">Animals Killed <span class="text-danger">*</span></label>
                        <input type="number" name="killed" id="killed" class="form-control @error('killed') is-invalid @enderror" 
                            value="{{ old('killed', $animalControl->killed) }}" min="0" required>
                        @error('killed')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="relocated" class="form-label">Animals Relocated <span class="text-danger">*</span></label>
                        <input type="number" name="relocated" id="relocated" class="form-control @error('relocated') is-invalid @enderror" 
                            value="{{ old('relocated', $animalControl->relocated) }}" min="0" required>
                        @error('relocated')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="scared" class="form-label">Animals Scared Away <span class="text-danger">*</span></label>
                        <input type="number" name="scared" id="scared" class="form-control @error('scared') is-invalid @enderror" 
                            value="{{ old('scared', $animalControl->scared) }}" min="0" required>
                        @error('scared')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="injured" class="form-label">Animals Injured <span class="text-danger">*</span></label>
                        <input type="number" name="injured" id="injured" class="form-control @error('injured') is-invalid @enderror" 
                            value="{{ old('injured', $animalControl->injured) }}" min="0" required>
                        @error('injured')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('animal_control_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Record
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 