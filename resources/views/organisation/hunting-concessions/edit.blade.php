@extends('layouts.organisation')

@section('title', 'Edit Hunting Concession')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Hunting Concession</h5>
                        <a href="{{ route('organisation.hunting-concessions.index', $organisation->slug ?? '') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('organisation.hunting-concessions.update', [$organisation->slug ?? '', $huntingConcession]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row g-3">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $huntingConcession->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hectarage" class="form-label">Hectarage</label>
                                    <input type="text" class="form-control @error('hectarage') is-invalid @enderror" 
                                           id="hectarage" name="hectarage" value="{{ old('hectarage', $huntingConcession->hectarage) }}">
                                    @error('hectarage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="safari_id" class="form-label">Safari Operator</label>
                                    <select class="form-control @error('safari_id') is-invalid @enderror" id="safari_id" name="safari_id">
                                        <option value="">Select Safari Operator</option>
                                        @foreach ($safaris as $safari)
                                            <option value="{{ $safari->id }}" {{ old('safari_id', $huntingConcession->safari_id) == $safari->id ? 'selected' : '' }}>{{ $safari->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('safari_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Location Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="text" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude', $huntingConcession->latitude) }}"
                                           placeholder="e.g., -17.8292">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="text" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude', $huntingConcession->longitude) }}"
                                           placeholder="e.g., 31.0522">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description', $huntingConcession->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Concession
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add any JavaScript for form validation or enhancement here
</script>
@endpush
@endsection
