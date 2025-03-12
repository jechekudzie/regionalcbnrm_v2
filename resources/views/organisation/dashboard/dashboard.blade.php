@extends('layouts.dashboard')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div>
                            <h4 class="text-dark mb-1">Welcome, {{$user->name}}</h4>
                            <p class="text-muted mb-0">Manage your organization access and details</p>
                        </div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">CRM</a></li>
                                <li class="breadcrumb-item active">Welcome</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{route('organisation.dashboard')}}" class="btn btn-soft-primary">
                            <i class="fa fa-arrow-left me-2"></i>Dashboard
                        </a>
                        @can('view-generic')
                            <a href="{{route('organisation.dashboard.rural-district-councils')}}" 
                               class="btn btn-soft-danger" 
                               target="_blank">
                                <i class="fas fa-building me-2"></i>RDC Organisations
                            </a>
                        @endcan
                        @if(auth()->user()->hasRole('super-admin'))
                            <a href="{{route('admin.organisations.manage')}}" 
                               class="btn btn-soft-success" 
                               target="_blank">
                                <i class="fas fa-user-shield me-2"></i>Admin Dashboard
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Organizations Grid -->
            <div class="row g-4">
                @foreach($organisations as $organisation)
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <div class="avatar-md mx-auto mb-3">
                                        <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-3">
                                            @php
                                                $words = explode(' ', $organisation->name);
                                                $initials = array_reduce($words, function($carry, $word) {
                                                    return $carry . strtoupper(substr($word, 0, 1));
                                                }, '');
                                                echo substr($initials, 0, 2);
                                            @endphp
                                        </div>
                                    </div>
                                    <h5 class="card-title mb-1">{{$organisation->name}}</h5>
                                    <p class="text-muted mb-0">{{$organisation->organisationType->name}}</p>
                                </div>

                                <div class="d-flex justify-content-between mb-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tie text-success me-2"></i>
                                        <span>
                                            @if($user->roles())
                                                {{$user->roles()->wherePivot('organisation_id', $organisation->id)->first()->name ?? 'Default Name'}}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-sitemap text-primary me-2"></i>
                                        <span>
                                            @if($organisation && method_exists($organisation, 'getAllChildren'))
                                                {{ count($organisation->getAllChildren()) }}
                                            @else
                                                0
                                            @endif
                                            children
                                        </span>
                                    </div>
                                </div>

                                <div class="progress mb-4" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 30%"></div>
                                    <div class="progress-bar bg-info" style="width: 20%"></div>
                                </div>

                                <a href="{{route('organisation.dashboard.index', $organisation->slug)}}" 
                                   class="btn btn-primary w-100" 
                                   target="_blank">
                                    <i class="fas fa-arrow-right-to-bracket me-2"></i>
                                    Enter Organisation
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        /* Card Styling */
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 12px;
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
        }

        /* Avatar Styling */
        .avatar-md {
            height: 4rem;
            width: 4rem;
        }

        .avatar-title {
            align-items: center;
            display: flex;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        /* Soft Button Variants */
        .btn-soft-primary {
            background-color: rgba(66, 133, 244, 0.1);
            color: #4285f4;
            border: none;
        }

        .btn-soft-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: none;
        }

        .btn-soft-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: none;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        /* Progress Bar */
        .progress {
            background-color: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }

        /* Text Styles */
        .card-title {
            font-weight: 600;
            color: #2c3e50;
        }

        .text-muted {
            color: #6c757d !important;
        }
    </style>
@stop
