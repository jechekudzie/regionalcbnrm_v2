@extends('layouts.organisation')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0" id="page-title">{{$user->name}} - {{$organisation->name}}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">CRM</a></li>
                                <li class="breadcrumb-item active">Species</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <div class="flex-grow-1">

                                        <a href="{{route('organisation.dashboard')}}" class="btn btn-info add-btn">
                                            <i class="fa fa-refresh"></i> Back To Dashboard
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="row">
                        <div class="col-12">

                            <div class="row">
                                @if(session()->has('errors'))
                                    @if($errors->any())

                                        @foreach($errors->all() as $error)
                                            <!-- Success Alert -->
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong> Errors! </strong> {{ $error }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                        aria-label="Close"></button>
                                            </div>
                                        @endforeach

                                    @endif
                                @endif
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Message!</strong> {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                    </div>
                                @endif

                                @foreach($species as $specie)
                                    <div class="col-xxl-2 col-lg-2">
                                        <div class="card card-overlay">
                                            <img style="height: 150px;margin-top: 50px;" class="card-img img-fluid"
                                                 src="{{asset($specie->avatar)}}"
                                                 alt="Card image">
                                            <div class="card-img-overlay p-0 d-flex flex-column">
                                                <div style="background-color: black" class="card-header bg-grey">
                                                    <h4 class="card-title text-center text-white mb-0">{{$specie->name}}</h4>
                                                </div>
                                                <div class="card-body">

                                                </div>
                                                <div class="card-footer bg-transparent text-center">
                                                    <a href="javascript:void(0);" class="link-light">Read More <i
                                                            class="ri-arrow-right-s-line align-middle ms-1 lh-1"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- end col -->
                                @endforeach

                            </div><!-- end row -->

                            <!-- Add Modal -->
                            <div class="modal fade" id="addSpecies" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="myModalLabel">Add Species</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">

                                            <form method="post" action="{{ route('admin.species.store') }}"
                                                  enctype="multipart/form-data">
                                                @csrf

                                                <!-- Name Input -->
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" id="name" name="name"
                                                           placeholder="Enter Species Name" required>
                                                </div>

                                                <!-- Scientific Name Input -->
                                                <div class="mb-3">
                                                    <label for="scientific" class="form-label">Scientific Name</label>
                                                    <input type="text" class="form-control" id="scientific"
                                                           name="scientific" placeholder="Enter Scientific Name">
                                                </div>

                                                <!-- Male Name Input -->
                                                <div class="mb-3">
                                                    <label for="male_name" class="form-label">Male Name</label>
                                                    <input type="text" class="form-control" id="male_name"
                                                           name="male_name" placeholder="Enter Male Species Name">
                                                </div>

                                                <!-- Female Name Input -->
                                                <div class="mb-3">
                                                    <label for="female_name" class="form-label">Female Name</label>
                                                    <input type="text" class="form-control" id="female_name"
                                                           name="female_name" placeholder="Enter Female Species Name">
                                                </div>

                                                <!-- Avatar Upload -->
                                                <div class="mb-3">
                                                    <label for="avatar" class="form-label">Avatar</label>
                                                    <input type="file" class="form-control" id="avatar" name="avatar">
                                                </div>

                                                <!-- Special Species Checkbox -->
                                                <div class="mb-3 form-check">
                                                    <input type="hidden" name="is_special" value="0">
                                                    <input type="checkbox" class="form-check-input" id="is_special" name="is_special" value="1" {{ old('is_special') ? 'checked' : '' }}>

                                                    <label class="form-check-label" for="is_special">Is Special?</label>
                                                </div>

                                                <!-- Submit Button -->
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </form>


                                        </div>
                                    </div>
                                    <!--end modal-content-->
                                </div>
                                <!--end modal-dialog-->
                            </div>

                        </div><!-- end col -->
                    </div>

                </div>

            </div>
            <!--end row-->

        </div>
        <!-- container-fluid -->
    </div>

@endsection
