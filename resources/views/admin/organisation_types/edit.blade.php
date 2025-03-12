@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-none border border-300 my-4">
                <div class="card-header p-4 border-bottom border-300 bg-soft">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 col-md">
                            <h4 class="text-900 mb-0">Edit Organization Type</h4>
                            @if($organisationType->parentOrganisationType())
                                <p class="mb-0 text-600">Child type of {{ $organisationType->parentOrganisationType()->name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="p-4">
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

                        <form action="{{ route('admin.organisation-types.update', $organisationType->slug) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label" for="name">Type Name</label>
                                <input class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       id="name" 
                                       type="text" 
                                       value="{{ old('name', $organisationType->name) }}"
                                       required/>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    Update Organization Type
                                </button>
                                <a href="{{ route('admin.organisation-types.manage') }}" 
                                   class="btn btn-light ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
