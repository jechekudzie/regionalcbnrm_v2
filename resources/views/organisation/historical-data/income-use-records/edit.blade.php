@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Edit Income Utilization Record
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-1 text-primary"></i>Edit Income Utilization Record
                </h5>
                <p class="mb-0 text-muted small">Update income utilization record for {{ $organisation->name }}</p>
            </div>
            <div>
                <a href="{{ route('income_use_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
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

        <form action="{{ route('income_use_records.update', [$organisation->slug, $incomeUseRecord->id]) }}" method="POST">
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
                                <option value="{{ $year }}" {{ old('period', $incomeUseRecord->period) == $year ? 'selected' : '' }}>{{ $year }}</option>
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
                            <h6 class="mb-0">Income Utilization Categories</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="administration_amount" class="form-label">Administration (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('administration_amount') is-invalid @enderror" id="administration_amount" name="administration_amount" placeholder="0.00" value="{{ old('administration_amount', $incomeUseRecord->administration_amount) }}" min="0" step="0.01" required>
                                        </div>
                                        @error('administration_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Administrative costs, salaries, office expenses, etc.</small>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="management_activities_amount" class="form-label">Management Activities (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('management_activities_amount') is-invalid @enderror" id="management_activities_amount" name="management_activities_amount" placeholder="0.00" value="{{ old('management_activities_amount', $incomeUseRecord->management_activities_amount) }}" min="0" step="0.01" required>
                                        </div>
                                        @error('management_activities_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Wildlife management, habitat restoration, etc.</small>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="social_services_amount" class="form-label">Social Services (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('social_services_amount') is-invalid @enderror" id="social_services_amount" name="social_services_amount" placeholder="0.00" value="{{ old('social_services_amount', $incomeUseRecord->social_services_amount) }}" min="0" step="0.01" required>
                                        </div>
                                        @error('social_services_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Education, healthcare, community development, etc.</small>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="law_enforcement_amount" class="form-label">Law Enforcement (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('law_enforcement_amount') is-invalid @enderror" id="law_enforcement_amount" name="law_enforcement_amount" placeholder="0.00" value="{{ old('law_enforcement_amount', $incomeUseRecord->law_enforcement_amount) }}" min="0" step="0.01" required>
                                        </div>
                                        @error('law_enforcement_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Anti-poaching, patrols, equipment, etc.</small>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="other_amount" class="form-label">Other (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('other_amount') is-invalid @enderror" id="other_amount" name="other_amount" placeholder="0.00" value="{{ old('other_amount', $incomeUseRecord->other_amount) }}" min="0" step="0.01" required>
                                        </div>
                                        @error('other_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="other_description" class="form-label">Other Description</label>
                                        <textarea class="form-control @error('other_description') is-invalid @enderror" id="other_description" name="other_description" rows="3" placeholder="Describe other expenses...">{{ old('other_description', $incomeUseRecord->other_description) }}</textarea>
                                        @error('other_description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Please provide details for other expenses</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('income_use_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Income Utilization Record
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection