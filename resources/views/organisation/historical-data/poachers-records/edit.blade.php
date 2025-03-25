@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Edit Poachers Record
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-edit me-1 text-primary"></i>Edit Poachers Record
                </h5>
                <p class="mb-0 text-muted small">Update poachers arrest information</p>
            </div>
            <div>
                <a href="{{ route('poachers_records.index', $organisation->slug) }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Poachers Records
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

        <form action="{{ route('poachers_records.update', [$organisation->slug, $poachersRecord]) }}" method="POST">
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
                                        <option value="{{ $year }}" {{ old('period', $poachersRecord->period) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                @error('period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The year when the poachers were arrested</div>
                            </div>

                            <div class="mb-3">
                                <label for="species_id" class="form-label">Species <span class="text-danger">*</span></label>
                                <select class="form-select @error('species_id') is-invalid @enderror" 
                                    id="species_id" name="species_id" required>
                                    <option value="">-- Select Species --</option>
                                    @foreach($species as $specie)
                                        <option value="{{ $specie->id }}" {{ old('species_id', $poachersRecord->species_id) == $specie->id ? 'selected' : '' }}>
                                            {{ $specie->name }} ({{ $specie->scientific }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('species_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-user-lock me-1"></i>Arrest Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="arrested" class="form-label">Number Arrested <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('arrested') is-invalid @enderror" 
                                    id="arrested" name="arrested" value="{{ old('arrested', $poachersRecord->arrested) }}" 
                                    min="0" required>
                                @error('arrested')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The number of poachers arrested</div>
                            </div>

                            <div class="mb-3">
                                <label for="bailed" class="form-label">Number Bailed <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('bailed') is-invalid @enderror" 
                                    id="bailed" name="bailed" value="{{ old('bailed', $poachersRecord->bailed) }}" 
                                    min="0" required>
                                @error('bailed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The number of poachers released on bail</div>
                            </div>

                            <div class="mb-3">
                                <label for="sentenced" class="form-label">Number Sentenced <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('sentenced') is-invalid @enderror" 
                                    id="sentenced" name="sentenced" value="{{ old('sentenced', $poachersRecord->sentenced) }}" 
                                    min="0" required>
                                @error('sentenced')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The number of poachers sentenced</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-sticky-note me-1"></i>Additional Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" name="notes" rows="3">{{ old('notes', $poachersRecord->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional: Any additional information about the arrests</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('poachers_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
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