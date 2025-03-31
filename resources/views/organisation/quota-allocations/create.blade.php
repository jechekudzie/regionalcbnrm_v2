@extends('layouts.organisation')

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Add New Quota Allocation
        </div>
        <div class="card-body">
            <form action="{{ route('organisation.quota-allocations.store', $organisation->slug) }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="species_id" class="form-label">Species</label>
                    <select name="species_id" id="species_id" class="form-select @error('species_id') is-invalid @enderror" required>
                        <option value="">Select Species</option>
                        @foreach($species as $s)
                            <option value="{{ $s->id }}" {{ old('species_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('species_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="hunting_quota" class="form-label">Hunting Quota</label>
                            <input type="number" class="form-control @error('hunting_quota') is-invalid @enderror" 
                                   id="hunting_quota" name="hunting_quota" value="{{ old('hunting_quota', 0) }}" 
                                   min="0" required>
                            @error('hunting_quota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="rational_killing_quota" class="form-label">Rational Killing Quota</label>
                            <input type="number" class="form-control @error('rational_killing_quota') is-invalid @enderror" 
                                   id="rational_killing_quota" name="rational_killing_quota" 
                                   value="{{ old('rational_killing_quota', 0) }}" min="0" required>
                            @error('rational_killing_quota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Create Quota Allocation</button>
                    <a href="{{ route('organisation.quota-allocations.index', $organisation->slug) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
