@extends('layouts.dashboard')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="text-dark mb-0">Rural District Councils Dashboard</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);" class="text-primary">CRM</a></li>
                        <li class="breadcrumb-item active">Rural District Councils</li>
                    </ol>
                </nav>
            </div>

            <div class="mb-4">
                <a href="{{route('organisation.dashboard')}}" class="btn btn-success px-4 py-2 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>
                    Return to dashboard
                </a>
            </div>

            <div class="row g-4">
                @foreach($ruralDistrictCouncils as $ruralDistrictCouncil)
                    <div class="col-xl-4 col-md-6">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <div class="avatar-md mx-auto mb-3 d-flex align-items-center justify-content-center bg-light rounded-circle">
                                        <span class="text-primary fs-3 fw-semibold">
                                            @php
                                                $words = explode(' ', $ruralDistrictCouncil->name);
                                                $initials = array_reduce($words, function($carry, $word) {
                                                    return $carry . strtoupper(substr($word, 0, 1));
                                                }, '');
                                                echo substr($initials, 0, 2);
                                            @endphp
                                        </span>
                                    </div>
                                    <h5 class="fw-semibold mb-1">{{$ruralDistrictCouncil->name}}</h5>
                                    <p class="text-muted small mb-0">{{$ruralDistrictCouncil->organisationType->name}}</p>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-sitemap text-primary me-2"></i>
                                        <span class="text-body-secondary">
                                            @if($ruralDistrictCouncil && method_exists($ruralDistrictCouncil, 'getAllChildren'))
                                                {{ count($ruralDistrictCouncil->getAllChildren()) }} children
                                            @else
                                                0 children
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users text-success me-2"></i>
                                        <span class="text-body-secondary">{{ $ruralDistrictCouncil->users_count ?? 0 }} users</span>
                                    </div>
                                </div>

                                <div class="progress mb-4" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 30%"></div>
                                    <div class="progress-bar bg-primary" style="width: 20%"></div>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="{{route('organisation.dashboard.index', $ruralDistrictCouncil->slug)}}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-arrow-right-to-bracket me-2"></i>
                                        Enter Organisation
                                    </a>
                                    <a href="javascript:void(0);" 
                                       class="btn btn-outline-primary"
                                       data-slug="{{$ruralDistrictCouncil->slug}}" 
                                       data-name="{{$ruralDistrictCouncil->name}}"
                                       data-id="{{$ruralDistrictCouncil->id}}">
                                        <i class="fas fa-sitemap me-2"></i>
                                        View Child Organisations
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        .page-content {
            padding: 1.5rem;
        }
        
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 0.75rem;
            border-color: #e5e7eb !important;
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        }

        .avatar-md {
            height: 3.5rem;
            width: 3.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: #6c757d;
        }

        .progress {
            background-color: #f3f4f6;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 0.3s ease;
        }

        .text-body-secondary {
            color: #6c757d !important;
        }

        .small {
            font-size: 0.875rem;
        }
    </style>
@endsection 