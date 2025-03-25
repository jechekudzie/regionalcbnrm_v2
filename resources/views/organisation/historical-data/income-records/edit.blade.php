@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Edit Income Distribution Record
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-money-bill-wave me-1 text-primary"></i>Edit Income Distribution Record
                </h5>
                <p class="mb-0 text-muted small">Update income distribution record for {{ $organisation->name }}</p>
            </div>
            <div>
                <a href="{{ route('income_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
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

        <form action="{{ route('income_records.update', [$organisation->slug, $incomeRecord->id]) }}" method="POST">
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
                                <option value="{{ $year }}" {{ old('period', $incomeRecord->period) == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                        @error('period')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="form-group">
                        <label for="rdc_share" class="form-label">RDC Share (USD) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control @error('rdc_share') is-invalid @enderror" id="rdc_share" name="rdc_share" placeholder="0.00" value="{{ old('rdc_share', $incomeRecord->rdc_share) }}" min="0" step="0.01" required>
                        </div>
                        @error('rdc_share')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="form-group">
                        <label for="community_share" class="form-label">Community Share (USD) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control @error('community_share') is-invalid @enderror" id="community_share" name="community_share" placeholder="0.00" value="{{ old('community_share', $incomeRecord->community_share) }}" min="0" step="0.01" required>
                        </div>
                        @error('community_share')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="form-group">
                        <label for="ca_share" class="form-label">CAMPFIRE Association Share (USD) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control @error('ca_share') is-invalid @enderror" id="ca_share" name="ca_share" placeholder="0.00" value="{{ old('ca_share', $incomeRecord->ca_share) }}" min="0" step="0.01" required>
                        </div>
                        @error('ca_share')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('income_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Income Record
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 