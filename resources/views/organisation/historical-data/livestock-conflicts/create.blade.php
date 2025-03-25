@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Add Livestock Conflict Record
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>Add New Livestock Conflict Record
                </h5>
                <p class="mb-0 text-muted small">Add historical livestock losses from wildlife conflicts</p>
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

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex">
                    <i class="fas fa-check-circle me-2 mt-1"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('livestock_conflict_records.store', $organisation->slug) }}" method="POST">
            @csrf
            
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
                            <option value="{{ $year }}" {{ old('period') == $year ? 'selected' : '' }}>{{ $year }}</option>
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
                            <option value="{{ $specie->id }}" {{ old('species_id') == $specie->id ? 'selected' : '' }}>
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
                            <option value="{{ $liveStockType->id }}" {{ old('live_stock_type_id') == $liveStockType->id ? 'selected' : '' }}>
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
                           value="{{ old('killed', 0) }}"
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
                           value="{{ old('injured', 0) }}"
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
                    <i class="fas fa-save me-1"></i>Save Record
                </button>
                <a href="{{ route('livestock_conflict_records.index', $organisation->slug) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 