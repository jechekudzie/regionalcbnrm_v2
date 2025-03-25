@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Edit Poaching Record
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-edit me-1 text-primary"></i>Edit Poaching Record
                </h5>
                <p class="mb-0 text-muted small">Update poaching incident information</p>
            </div>
            <div>
                <a href="{{ route('poaching_records.index', $organisation->slug) }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Poaching Records
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

        <form action="{{ route('poaching_records.update', [$organisation->slug, $poachingRecord]) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="organisation_id" value="{{ $organisation->id }}">

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-1"></i>Basic Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="period" class="form-label">Year <span class="text-danger">*</span></label>
                                <select class="form-select @error('period') is-invalid @enderror" 
                                    id="period" name="period" required>
                                    <option value="">-- Select Year --</option>
                                    @php
                                        $currentYear = date('Y');
                                        $startYear = 2000;
                                    @endphp
                                    @for($year = $currentYear; $year >= $startYear; $year--)
                                        <option value="{{ $year }}" {{ old('period', $poachingRecord->period) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                @error('period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The year when the poaching incident occurred</div>
                            </div>

                            <div class="mb-3">
                                <label for="species_id" class="form-label">Species <span class="text-danger">*</span></label>
                                <select class="form-select @error('species_id') is-invalid @enderror" 
                                    id="species_id" name="species_id" required>
                                    <option value="">-- Select Species --</option>
                                    @foreach($species as $specie)
                                        <option value="{{ $specie->id }}" {{ old('species_id', $poachingRecord->species_id) == $specie->id ? 'selected' : '' }}>
                                            {{ $specie->name }} ({{ $specie->scientific_name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('species_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="poaching_method_id" class="form-label">Poaching Method <span class="text-danger">*</span></label>
                                <select class="form-select @error('poaching_method_id') is-invalid @enderror" 
                                    id="poaching_method_id" name="poaching_method_id" required>
                                    <option value="">-- Select Method --</option>
                                    @foreach($poachingMethods as $method)
                                        <option value="{{ $method->id }}" {{ old('poaching_method_id', $poachingRecord->poaching_method_id) == $method->id ? 'selected' : '' }}>
                                            {{ $method->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('poaching_method_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-chart-bar me-1"></i>Incident Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="number" class="form-label">Number of Animals <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('number') is-invalid @enderror" 
                                    id="number" name="number" value="{{ old('number', $poachingRecord->number) }}" 
                                    min="1" required>
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The number of animals poached in this incident</div>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                    id="location" name="location" value="{{ old('location', $poachingRecord->location) }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional: Specific location where the incident occurred</div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                    id="notes" name="notes" rows="3">{{ old('notes', $poachingRecord->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional: Any additional information about the incident</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('poaching_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Update Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize any form enhancements here
    });
</script>
@endpush 