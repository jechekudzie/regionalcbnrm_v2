@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Edit Income Beneficiary Record
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-users me-1 text-primary"></i>Edit Income Beneficiary Record
                </h5>
                <p class="mb-0 text-muted small">Update income beneficiary record for {{ $organisation->name }}</p>
            </div>
            <div>
                <a href="{{ route('income_beneficiary_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex">
                <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                <div>
                    <strong>Error!</strong> Please check the form for errors.
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form action="{{ route('income_beneficiary_records.update', [$organisation->slug, $incomeBeneficiaryRecord->id]) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row g-4">
                <div class="col-md-6 col-xl-3">
                    <div class="form-group">
                        <label for="period" class="form-label">Year <span class="text-danger">*</span></label>
                        <select class="form-select" id="period" name="period" required>
                            <option value="">Select Year</option>
                            @php
                                $currentYear = date('Y');
                                $startYear = 2000; // You can adjust this as needed
                            @endphp
                            @for ($year = $currentYear; $year >= $startYear; $year--)
                                <option value="{{ $year }}" {{ old('period', $incomeBeneficiaryRecord->period) == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                        @error('period')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Beneficiary Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="households" class="form-label">Number of Households <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('households') is-invalid @enderror" id="households" name="households" placeholder="0" value="{{ old('households', $incomeBeneficiaryRecord->households) }}" min="0" required>
                                        @error('households')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Total number of households benefiting from income</small>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="males" class="form-label">Number of Males <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('males') is-invalid @enderror" id="males" name="males" placeholder="0" value="{{ old('males', $incomeBeneficiaryRecord->males) }}" min="0" required>
                                        @error('males')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Total number of male beneficiaries</small>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="females" class="form-label">Number of Females <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('females') is-invalid @enderror" id="females" name="females" placeholder="0" value="{{ old('females', $incomeBeneficiaryRecord->females) }}" min="0" required>
                                        @error('females')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Total number of female beneficiaries</small>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Additional notes about the beneficiaries...">{{ old('notes', $incomeBeneficiaryRecord->notes) }}</textarea>
                                        @error('notes')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Optional additional information about the beneficiaries</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('income_beneficiary_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Income Beneficiary Record
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 