@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Edit Source of Income Record
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-money-bill-wave me-1 text-primary"></i>Edit Source of Income Record
                </h5>
                <p class="mb-0 text-muted small">Update source of income record for {{ $organisation->name }}</p>
            </div>
            <div>
                <a href="{{ route('source_of_income_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
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

        <form action="{{ route('source_of_income_records.update', [$organisation->slug, $sourceOfIncomeRecord->id]) }}" method="POST">
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
                                <option value="{{ $year }}" {{ old('period', $sourceOfIncomeRecord->period) == $year ? 'selected' : '' }}>{{ $year }}</option>
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
                            <h6 class="mb-0">Income Sources</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="trophy_fee_amount" class="form-label">Trophy Fee (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('trophy_fee_amount') is-invalid @enderror" id="trophy_fee_amount" name="trophy_fee_amount" placeholder="0.00" value="{{ old('trophy_fee_amount', $sourceOfIncomeRecord->trophy_fee_amount) }}" min="0" step="0.01" required>
                                        </div>
                                        @error('trophy_fee_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Income from trophy fees</small>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="hides_amount" class="form-label">Hides (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('hides_amount') is-invalid @enderror" id="hides_amount" name="hides_amount" placeholder="0.00" value="{{ old('hides_amount', $sourceOfIncomeRecord->hides_amount) }}" min="0" step="0.01" required>
                                        </div>
                                        @error('hides_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Income from animal hides</small>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="meat_amount" class="form-label">Meat (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('meat_amount') is-invalid @enderror" id="meat_amount" name="meat_amount" placeholder="0.00" value="{{ old('meat_amount', $sourceOfIncomeRecord->meat_amount) }}" min="0" step="0.01" required>
                                        </div>
                                        @error('meat_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Income from meat sales</small>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="hunting_concession_fee_amount" class="form-label">Hunting Concession Fee (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('hunting_concession_fee_amount') is-invalid @enderror" id="hunting_concession_fee_amount" name="hunting_concession_fee_amount" placeholder="0.00" value="{{ old('hunting_concession_fee_amount', $sourceOfIncomeRecord->hunting_concession_fee_amount) }}" min="0" step="0.01" required>
                                        </div>
                                        @error('hunting_concession_fee_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Income from hunting concession fees</small>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="photographic_fee_amount" class="form-label">Photographic Fee (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('photographic_fee_amount') is-invalid @enderror" id="photographic_fee_amount" name="photographic_fee_amount" placeholder="0.00" value="{{ old('photographic_fee_amount', $sourceOfIncomeRecord->photographic_fee_amount) }}" min="0" step="0.01" required>
                                        </div>
                                        @error('photographic_fee_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Income from photographic tourism</small>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="other_amount" class="form-label">Other (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('other_amount') is-invalid @enderror" id="other_amount" name="other_amount" placeholder="0.00" value="{{ old('other_amount', $sourceOfIncomeRecord->other_amount) }}" min="0" step="0.01" required>
                                        </div>
                                        @error('other_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="other_description" class="form-label">Other Description</label>
                                        <textarea class="form-control @error('other_description') is-invalid @enderror" id="other_description" name="other_description" rows="3" placeholder="Describe other income sources...">{{ old('other_description', $sourceOfIncomeRecord->other_description) }}</textarea>
                                        @error('other_description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Please provide details for other income sources</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('source_of_income_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Source of Income Record
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 