@extends('layouts.organisation')

@section('title', 'Add New Species')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add New Species</h5>
                        <a href="{{ route('species.index', $organisation->slug ?? '') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('species.store', $organisation->slug ?? '') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <!-- Basic Information -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="scientific" class="form-label">Scientific Name</label>
                                            <input type="text" class="form-control @error('scientific') is-invalid @enderror" 
                                                   id="scientific" name="scientific" value="{{ old('scientific') }}">
                                            @error('scientific')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Gender Names -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="male_name" class="form-label">Male Name</label>
                                            <input type="text" class="form-control @error('male_name') is-invalid @enderror" 
                                                   id="male_name" name="male_name" value="{{ old('male_name') }}">
                                            @error('male_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="female_name" class="form-label">Female Name</label>
                                            <input type="text" class="form-control @error('female_name') is-invalid @enderror" 
                                                   id="female_name" name="female_name" value="{{ old('female_name') }}">
                                            @error('female_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Image Upload -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="avatar" class="form-label">Species Image</label>
                                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                                   id="avatar" name="avatar" accept="image/*">
                                            @error('avatar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="mt-2">
                                                <img id="avatar-preview" src="{{ asset('images/default-species.jpg') }}" 
                                                     class="img-fluid rounded" alt="Species Preview">
                                            </div>
                                        </div>

                                        <!-- Status Toggle -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="is_special" 
                                                       name="is_special" value="1" {{ old('is_special') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_special">Special Species</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Species
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
    // Image preview
    document.getElementById('avatar').addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('avatar-preview').src = event.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    });
</script>
@endpush
@endsection
