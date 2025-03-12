@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-none border border-300 my-4">
                <div class="card-header p-4 border-bottom border-300 bg-soft">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 col-md">
                            <h4 class="text-900 mb-0">Create New {{ $type->name }}</h4>
                            <p class="mb-0 text-600">
                                @if($isSameLevel)
                                    At same level as others
                                @else
                                    Under {{ $parentName }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="p-4">
                        <form action="{{ route('admin.organisations.store-child') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="name">Name</label>
                                <input class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       id="name" 
                                       type="text" 
                                       value="{{ old('name') }}"
                                       placeholder="Enter name" 
                                       required/>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control" name="description" id="description" 
                                          rows="3"></textarea>
                            </div>

                            <!-- Hidden fields -->
                            <input type="hidden" name="parent_id" value="{{ $parent ? $parent->id : '' }}">
                            <input type="hidden" name="organisation_type_id" value="{{ $type->id }}">

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    Create {{ $type->name }}
                                </button>
                                <a href="{{ route('admin.organisations.manage') }}" 
                                   class="btn btn-light ms-2">Cancel</a>
                            </div>
                        </form>

                        @if (session('error'))
                            <div class="alert alert-danger mt-3">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-soft {
    background-color: #f8f9fa;
}
.form-label {
    font-weight: 600;
    color: #444;
}
</style>
@endsection 