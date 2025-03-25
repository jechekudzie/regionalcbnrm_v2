@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Add Human Conflict Record
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-user-injured me-1 text-primary"></i>Add Human Conflict Record
                </h5>
                <p class="mb-0 text-muted small">Record historical human casualties from wildlife conflicts</p>
            </div>
            <div>
                <a href="{{ route('human_conflict_records.index', $organisation->slug) }}" class="btn btn-outline-primary">
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

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex">
                    <i class="fas fa-check-circle me-2 mt-1"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('human_conflict_records.store', $organisation->slug) }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="period" class="form-label">Year <span class="text-danger">*</span></label>
                        <select name="period" id="period" class="form-select @error('period') is-invalid @enderror" required>
                            <option value="">Select Year</option>
                            @for ($year = date('Y'); $year >= 1990; $year--)
                                <option value="{{ $year }}" {{ old('period') == $year ? 'selected' : '' }}>{{ $year }}</option>
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
                                <option value="{{ $specie->id }}" {{ old('species_id') == $specie->id ? 'selected' : '' }}>
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
                        <label for="gender_id" class="form-label">Gender <span class="text-danger">*</span></label>
                        <select name="gender_id" id="gender_id" class="form-select @error('gender_id') is-invalid @enderror" required>
                            <option value="">Select Gender</option>
                            @foreach ($genders as $gender)
                                <option value="{{ $gender->id }}" {{ old('gender_id') == $gender->id ? 'selected' : '' }}>
                                    {{ $gender->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('gender_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="deaths" class="form-label">Deaths <span class="text-danger">*</span></label>
                        <input type="number" name="deaths" id="deaths" class="form-control @error('deaths') is-invalid @enderror" 
                            value="{{ old('deaths', 0) }}" min="0" required>
                        @error('deaths')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="injured" class="form-label">Injured <span class="text-danger">*</span></label>
                        <input type="number" name="injured" id="injured" class="form-control @error('injured') is-invalid @enderror" 
                            value="{{ old('injured', 0) }}" min="0" required>
                        @error('injured')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('human_conflict_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Record
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 