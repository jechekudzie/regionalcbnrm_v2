@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Edit Livestock Conflict Record
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2 text-primary"></i>Edit Livestock Conflict Record
                </h5>
                <p class="mb-0 text-muted small">Update historical livestock losses from wildlife conflicts</p>
            </div>
            <div class="col-auto align-self-center">
                <a href="{{ route('livestock_conflict_records.index', $organisation->slug) }}" class="btn btn-outline-primary">
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

        <form action="{{ route('livestock_conflict_records.update', [$organisation->slug, $liveStockConflict]) }}" method="POST">
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
                            <option value="{{ $year }}" {{ old('period', $liveStockConflict->period) == $year ? 'selected' : '' }}>{{ $year }}</option>
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
                            <option value="{{ $specie->id }}" {{ old('species_id', $liveStockConflict->species_id) == $specie->id ? 'selected' : '' }}>
                                {{ $specie->name }} ({{ $specie->scientific_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('species_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Livestock Type -->
                <div class="col-md-6">
                    <label class="form-label" for="live_stock_type_id">Livestock Type</label>
                    <select class="form-select @error('live_stock_type_id') is-invalid @enderror" 
                            id="live_stock_type_id" 
                            name="live_stock_type_id" 
                            required>
                        <option value="">Select Livestock Type</option>
                        @foreach($liveStockTypes as $liveStockType)
                            <option value="{{ $liveStockType->id }}" {{ old('live_stock_type_id', $liveStockConflict->live_stock_type_id) == $liveStockType->id ? 'selected' : '' }}>
                                {{ $liveStockType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('live_stock_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Killed -->
                <div class="col-md-3">
                    <label class="form-label" for="killed">Number Killed</label>
                    <input type="number" 
                           class="form-control @error('killed') is-invalid @enderror" 
                           id="killed" 
                           name="killed" 
                           value="{{ old('killed', $liveStockConflict->killed) }}"
                           min="0"
                           required>
                    @error('killed')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Injured -->
                <div class="col-md-3">
                    <label class="form-label" for="injured">Number Injured</label>
                    <input type="number" 
                           class="form-control @error('injured') is-invalid @enderror" 
                           id="injured" 
                           name="injured" 
                           value="{{ old('injured', $liveStockConflict->injured) }}"
                           min="0"
                           required>
                    @error('injured')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Update Record
                </button>
                <a href="{{ route('livestock_conflict_records.index', $organisation->slug) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 