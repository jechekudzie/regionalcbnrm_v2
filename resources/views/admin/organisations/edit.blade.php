@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow-none border border-300 my-4">
                <div class="card-header p-4 border-bottom border-300 bg-soft">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 col-md">
                            <h4 class="text-900 mb-0">
                                @if($organisation->parentOrganisation)
                                    Edit {{ $organisation->organisationType->name }} for {{ $organisation->parentOrganisation->name }}
                                @else
                                    Edit {{ $organisation->organisationType->name }}
                                @endif
                            </h4>
                            <p class="mb-0 text-600">
                                Current Name: {{ $organisation->name }}
                                @if($organisation->parentOrganisation)
                                    <br>Reports to: {{ $organisation->parentOrganisation->name }} 
                                    ({{ $organisation->parentOrganisation->organisationType->name }})
                                @else
                                    <br>Root Level Organization
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="p-4">
                        <form action="{{ route('admin.organisations.update', $organisation->slug) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-3">
                                <label class="form-label" for="name">{{ $organisation->organisationType->name }} Name</label>
                                <input class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       id="name" 
                                       type="text" 
                                       value="{{ old('name', $organisation->name) }}"
                                       required/>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" 
                                          id="description" 
                                          rows="3">{{ old('description', $organisation->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    Update {{ $organisation->organisationType->name }}
                                </button>
                                <a href="{{ route('admin.organisations.manage') }}" 
                                   class="btn btn-light ms-2">Cancel</a>
                            </div>
                        </form>
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
