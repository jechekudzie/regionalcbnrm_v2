@extends('layouts.dashboard')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Welcome {{$user->name}}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">CRM</a></li>
                                <li class="breadcrumb-item active">Welcome</li>
                            </ol>
                        </div>

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
                                        <i class="fa fa-arrow-left"></i>
                                        Return to dashboard
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
                <div class="col-xxl-12">
                    <div class="row">
                        @foreach($ruralDistrictCouncils as $ruralDistrictCouncil)
                            <div class="col-lg-4 col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="text-center fs-15 fw-semibold mb-8">{{$ruralDistrictCouncil->name}}</h5>
                                        <div class="d-flex flex-wrap justify-content-evenly">
                                            <p class="text-muted text-end">
                                                <i class="fa fa-sitemap text-primary fs-18 align-right me-2"></i>
                                                child organisations @if($ruralDistrictCouncil && method_exists($ruralDistrictCouncil, 'getAllChildren'))
                                                    ( {{ count($ruralDistrictCouncil->getAllChildren()) }})
                                                @else
                                                    child organisations (0)
                                                @endif

                                            </p>
                                        </div>
                                    </div>
                                    <div class="progress animated-progress rounded-bottom rounded-0"
                                         style="height: 6px;">
                                        <div class="progress-bar bg-success rounded-0" role="progressbar"
                                             style="width: 30%" aria-valuenow="30" aria-valuemin="0"
                                             aria-valuemax="100"></div>

                                        <div class="progress-bar rounded-0" role="progressbar" style="width: 20%"
                                             aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <!-- card footer -->
                                    <div class="card-footer">
                                        <!-- Buttons Grid -->
                                        <div class="d-grid gap-2">
                                            <a style="margin: 3px;"
                                               href="{{route('organisation.dashboard.index', $ruralDistrictCouncil->slug)}}"
                                               class="btn btn-danger  btn-sm  float-start">Enter This Organisation
                                                <i class="ri-arrow-right-s-line align-middle ms-1 lh-1"></i>
                                            </a>

                                            <a style="margin: 3px;" href="javascript:void(0);"
                                               class="btn btn-primary btn-sm float-end"
                                               data-slug="{{$ruralDistrictCouncil->slug}}" data-name="{{$ruralDistrictCouncil->name}}"
                                               data-id="{{$ruralDistrictCouncil->id}}"
                                            >View Child Organisations
                                                <i class="ri-arrow-right-s-line align-middle ms-1 lh-1"></i>
                                            </a>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        @endforeach
                        <!-- end col -->
                    </div>
                    <!--end card-->
                </div>
            </div>
            <!--end row-->

        </div>
        <!-- container-fluid -->
    </div>
@stop
