<!DOCTYPE html>
<html lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>@yield('title', 'Regional CBNRM')</title>


    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicons/favicon.ico">
    <link rel="manifest" href="assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="assets/img/favicons/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">
    <script src="{{ asset('/backend/vendors/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('/backend/vendors/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/config.js') }}"></script>


    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap"
        rel="stylesheet">
    <link href="{{ asset('/backend/vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="{{ asset('backend/assets/css/theme-rtl.min.css') }}" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="{{ asset('backend/assets/css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{ asset('backend/assets/css/user-rtl.min.css') }}" type="text/css" rel="stylesheet"
        id="user-style-rtl">
    <link href="{{ asset('backend/assets/css/user.min.css') }}" type="text/css" rel="stylesheet"
        id="user-style-default">

    <script>
        var phoenixIsRTL = window.config.config.phoenixIsRTL;
        if (phoenixIsRTL) {
            var linkDefault = document.getElementById('style-default');
            var userLinkDefault = document.getElementById('user-style-default');
            linkDefault.setAttribute('disabled', true);
            userLinkDefault.setAttribute('disabled', true);
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            var linkRTL = document.getElementById('style-rtl');
            var userLinkRTL = document.getElementById('user-style-rtl');
            linkRTL.setAttribute('disabled', true);
            userLinkRTL.setAttribute('disabled', true);
        }
    </script>
    <link href="{{ asset('/backend/vendors/leaflet/leaflet.css') }}" rel="stylesheet">
    <link href="{{ asset('/backend/vendors/leaflet.markercluster/MarkerCluster.css') }}" rel="stylesheet">
    <link href="{{ asset('/backend/vendors/leaflet.markercluster/MarkerCluster.Default.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/flatpickr/flatpickr.min.css') }}" rel="stylesheet">


    <!-- Gijgo config file -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .gj-tree-bootstrap-4 ul.gj-list-bootstrap li.active {
            background-color: gray !important;
        }

        .nav-link.active {
            color: #2c7be5 !important; /* Primary blue color */
            font-weight: 600;
            background-color: rgba(44, 123, 229, 0.1);
        }

        .nav-link.active .nav-link-text {
            color: #2c7be5 !important;
        }

        .nav-category-header {
            padding: 0.5rem 1rem 0.25rem;
        }
        .nav-category-text {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6c757d;
        }
    </style>
    @stack('head')
</head>


<body>

    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <nav class="navbar navbar-vertical navbar-expand-lg">
            <script>
                var navbarStyle = window.config.config.phoenixNavbarStyle;
                if (navbarStyle && navbarStyle !== 'transparent') {
                    document.querySelector('body').classList.add(`navbar-${navbarStyle}`);
                }
            </script>
            <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
                <!-- scrollbar removed-->
                <div class="navbar-vertical-content">
                    <ul class="navbar-nav flex-column" id="navbarVerticalNav">

                        <!-- Real Time Data Section -->
                        <li class="nav-item">
                            <p class="navbar-vertical-label">System Modules</p>
                            <hr class="navbar-vertical-line" />
                            <div class="nav-item-wrapper">
                                <a class="nav-link dropdown-indicator label-1 {{ Request::routeIs('admin.*') ? 'active' : '' }}" href="#admin" role="button"
                                    data-bs-toggle="collapse" aria-expanded="{{ Request::routeIs('admin.*') ? 'true' : 'false' }}" aria-controls="admin">
                                    <div class="d-flex align-items-center">
                                        <div class="dropdown-indicator-icon">
                                            <span class="fas fa-caret-right"></span>
                                        </div>
                                        <span class="nav-link-icon">
                                            <span class="fas fa-database"></span>
                                        </span>
                                        <span class="nav-link-text">Regional CBNRM</span>
                                    </div>
                                </a>
                                <div class="parent-wrapper label-1">
                                    <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse"
                                        id="admin">
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('species.*') ? 'active' : '' }}" href="{{ route('species.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Species</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('organisation.hunting-concessions.*') ? 'active' : '' }}" href="{{ route('organisation.hunting-concessions.index', $organisation->slug) }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Hunting Concessions</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('organisation.hunting-activities.*') ? 'active' : '' }}" href="{{ route('organisation.hunting-activities.index', $organisation->slug) }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Hunting Activities</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('organisation.quota-allocations.*') ? 'active' : '' }}" href="{{ route('organisation.quota-allocations.index', $organisation->slug) }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Quota Allocations</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('organisation.wildlife-conflicts.*') ? 'active' : '' }}" href="{{ route('organisation.wildlife-conflicts.index', $organisation->slug) }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Wildlife Conflicts</span>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>

                        <!-- Historical Data Section -->
                        <li class="nav-item">
                            <p class="navbar-vertical-label"> Historical Data</p>
                            <hr class="navbar-vertical-line" />
                            <div class="nav-item-wrapper">
                                <a class="nav-link dropdown-indicator label-1 {{ Request::routeIs('hunting_records.*') || Request::routeIs('crop_conflict_records.*') || Request::routeIs('livestock_conflict_records.*') || Request::routeIs('human_conflict_records.*') || Request::routeIs('animal_control_records.*') || Request::routeIs('poaching_records.*') || Request::routeIs('poachers_records.*') ? 'active' : '' }}" href="#historical" role="button"
                                    data-bs-toggle="collapse" aria-expanded="{{ Request::routeIs('hunting_records.*') || Request::routeIs('crop_conflict_records.*') || Request::routeIs('livestock_conflict_records.*') || Request::routeIs('human_conflict_records.*') || Request::routeIs('animal_control_records.*') || Request::routeIs('poaching_records.*') || Request::routeIs('poachers_records.*') ? 'true' : 'false' }}" aria-controls="historical">
                                    <div class="d-flex align-items-center">
                                        <div class="dropdown-indicator-icon">
                                            <span class="fas fa-caret-right"></span>
                                        </div>
                                        <span class="nav-link-icon">
                                            <span class="fas fa-history"></span>
                                        </span>
                                        <span class="nav-link-text">Historical Data</span>
                                    </div>
                                </a>
                                <div class="parent-wrapper label-1">
                                    <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse"
                                        id="historical">
                                        <!-- Hunting & Utilization -->
                                        <li class="nav-item">
                                            <div class="nav-category-header">
                                                <span class="nav-category-text">Hunting & Utilization</span>
                                            </div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('hunting_records.*') ? 'active' : '' }}" href="{{ route('hunting_records.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Allocation & Utilization</span>
                                                </div>
                                            </a>
                                        </li>

                                        <!-- Human-Wildlife Conflict -->
                                        <li class="nav-item">
                                            <div class="nav-category-header mt-3">
                                                <span class="nav-category-text">Human-Wildlife Conflict</span>
                                            </div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('crop_conflict_records.*') ? 'active' : '' }}" href="{{ route('crop_conflict_records.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">HWC - Crops</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('livestock_conflict_records.*') ? 'active' : '' }}" href="{{ route('livestock_conflict_records.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">HWC - Livestock</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('human_conflict_records.*') ? 'active' : '' }}" href="{{ route('human_conflict_records.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">HWC - Human</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('animal_control_records.*') ? 'active' : '' }}" href="{{ route('animal_control_records.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Problem Animal Control</span>
                                                </div>
                                            </a>
                                        </li>

                                        <!-- Poaching & Law Enforcement -->
                                        <li class="nav-item">
                                            <div class="nav-category-header mt-3">
                                                <span class="nav-category-text">Poaching & Law Enforcement</span>
                                            </div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('poaching_records.*') ? 'active' : '' }}" href="{{ route('poaching_records.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Poaching Incidents</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('poachers_records.*') ? 'active' : '' }}" href="{{ route('poachers_records.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Poachers Arrests</span>
                                                </div>
                                            </a>
                                        </li>

                                        <!-- Financial Records -->
                                        <li class="nav-item">
                                            <div class="nav-category-header mt-3">
                                                <span class="nav-category-text">Financial Records</span>
                                            </div>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('income_records.*') ? 'active' : '' }}" href="{{ route('income_records.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Income Distribution</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('income_use_records.*') ? 'active' : '' }}" href="{{ route('income_use_records.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Income Utilization</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('source_of_income_records.*') ? 'active' : '' }}" href="{{ route('source_of_income_records.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Income Sources</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('income_beneficiary_records.*') ? 'active' : '' }}" href="{{ route('income_beneficiary_records.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Income Beneficiaries</span>
                                                </div>
                                            </a>
                                        </li>

                                        <!-- Human Resources -->
                                        <li class="nav-item">
                                            <div class="nav-category-header mt-3">
                                                <span class="nav-category-text">Human Resources</span>
                                            </div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('human-resource-records.*') ? 'active' : '' }}" href="{{ route('human-resource-records.index', $organisation->slug ?? '') }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="nav-link-text">Staff Records</span>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>
            <div class="navbar-vertical-footer">
                <button
                    class="btn navbar-vertical-toggle border-0 fw-semi-bold w-100 white-space-nowrap d-flex align-items-center">
                    <span class="uil uil-left-arrow-to-left fs-0"></span><span
                        class="uil uil-arrow-from-right fs-0"></span><span
                        class="navbar-vertical-footer-text ms-2">Collapsed View</span></button>
            </div>
        </nav>

        <nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="navbar-logo">
                    <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent"
                        type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse"
                        aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
                        <span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
                    <a class="navbar-brand me-1 me-sm-3" href="{{ url('/admin') }}">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center">
                                <!-- <img src="{{ asset('logo/logo.png') }}" alt="phoenix" width="80" /> -->
                                <p class="logo-text ms-2 d-none d-sm-block">Regional CBNRM</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="search-box navbar-top-search-box d-none d-lg-block" data-list='{"valueNames":["title"]}'
                    style="width:25rem;">
                    <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
                        <input class="form-control search-input fuzzy-search rounded-pill form-control-sm"
                            type="search" placeholder="Search..." aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>

                    </form>
                    <div class="btn-close position-absolute end-0 top-50 translate-middle cursor-pointer shadow-none"
                        data-bs-dismiss="search">
                        <button class="btn btn-link btn-close-falcon p-0" aria-label="Close"></button>
                    </div>
                    <div class="dropdown-menu border border-300 font-base start-0 py-0 overflow-hidden w-100">
                        <div class="scrollbar-overlay" style="max-height: 30rem;">
                            <div class="list pb-3">
                                <h6 class="dropdown-header text-1000 fs--2 py-2">24 <span
                                        class="text-500">results</span>
                                </h6>
                                <hr class="text-200 my-0" />
                                <h6 class="dropdown-header text-1000 fs--1 border-bottom border-200 py-2 lh-sm">
                                    Recently
                                    Searched </h6>

                                <hr class="text-200 my-0" />
                                <h6 class="dropdown-header text-1000 fs--1 border-bottom border-200 py-2 lh-sm">
                                    Products</h6>
                                <div class="py-2"><a class="dropdown-item py-2 d-flex align-items-center"
                                        href="apps/e-commerce/landing/product-details.html">
                                        <div class="file-thumbnail me-2"><img class="h-100 w-100 fit-cover rounded-3"
                                                src="assets/img/products/60x60/3.png" alt="" />
                                        </div>
                                        <div class="flex-1">
                                            <h6 class="mb-0 text-1000 title">Battery</h6>

                                        </div>
                                    </a>
                                    <a class="dropdown-item py-2 d-flex align-items-center"
                                        href="apps/e-commerce/landing/product-details.html">
                                        <div class="file-thumbnail me-2"><img class="img-fluid"
                                                src="assets/img/products/60x60/3.png" alt="" />
                                        </div>
                                        <div class="flex-1">
                                            <h6 class="mb-0 text-1000 title">Solar Pannels</h6>
                                        </div>
                                    </a>

                                </div>
                                <hr class="text-200 my-0" />
                            </div>
                            <div class="text-center">
                                <p class="fallback fw-bold fs-1 d-none">No Result Found.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="navbar-nav navbar-nav-icons flex-row">
                    <li class="nav-item dropdown">
                        <a class="nav-link" id="navbarDropdownOrganisation" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span class="d-none d-sm-inline-block me-2">
                                    @if(request()->route('organisation') && !is_string(request()->route('organisation')))
                                        {{ request()->route('organisation')->name }}
                                    @else
                                        Organizations
                                    @endif
                                </span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 0C6.06875 0 4.5 1.56875 4.5 3.5C4.5 5.43125 6.06875 7 8 7C9.93125 7 11.5 5.43125 11.5 3.5C11.5 1.56875 9.93125 0 8 0ZM8 1.5C9.10313 1.5 10 2.39687 10 3.5C10 4.60313 9.10313 5.5 8 5.5C6.89687 5.5 6 4.60313 6 3.5C6 2.39687 6.89687 1.5 8 1.5Z" fill="currentColor"/>
                                    <path d="M10 8H6C2.68749 8 0 10.6875 0 14V15.5C0 15.775 0.225 16 0.5 16H15.5C15.775 16 16 15.775 16 15.5V14C16 10.6875 13.3125 8 10 8ZM14.5 14.5H1.5V14C1.5 11.5 3.5 9.5 6 9.5H10C12.5 9.5 14.5 11.5 14.5 14V14.5Z" fill="currentColor"/>
                                </svg>
                            </div>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-nine-dots shadow border" aria-labelledby="navbarDropdownOrganisation">
                            <div class="card bg-body-emphasis position-relative border-0">
                                <div class="card-body pt-3 px-3 pb-0 overflow-auto scrollbar" style="height: 20rem;">
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-300 pb-2 mb-2">
                                        <h5 class="mb-0 text-body-emphasis">Your Organisations</h5>
                                    </div>
                                    @if(auth()->check() && auth()->user()->organisations->count() > 0)
                                        <div class="row g-2 py-1">
                                            @foreach(auth()->user()->organisations as $organisation)
                                                @php
                                                    $routeOrg = request()->route('organisation');
                                                    // Check if the route parameter is an Organization model
                                                    $isActive = false;
                                                    if (is_object($routeOrg) && method_exists($routeOrg, 'getKey')) {
                                                        $isActive = $routeOrg->getKey() == $organisation->getKey();
                                                    }
                                                @endphp
                                                <div class="col-12">
                                                    <a href="{{ route('organisation.dashboard.index', $organisation->slug) }}" class="d-block bg-body-secondary-hover p-2 rounded-3 text-decoration-none mb-1 position-relative {{ $isActive ? 'bg-soft-primary' : '' }}">
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-xl me-2 bg-soft-primary text-primary rounded-circle text-center">
                                                                <span class="avatar-name">
                                                                    @php
                                                                        $words = explode(' ', $organisation->name);
                                                                        $initials = array_reduce($words, function($carry, $word) {
                                                                            return $carry . strtoupper(substr($word, 0, 1));
                                                                        }, '');
                                                                        echo substr($initials, 0, 2);
                                                                    @endphp
                                                                </span>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-0 text-body-emphasis">{{ $organisation->name }}</h6>
                                                                <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1">{{ $organisation->organisationType->name ?? 'Organization' }}</p>
                                                            </div>
                                                            @if($isActive)
                                                                <span class="position-absolute end-0 top-50 translate-middle-y me-2 text-success">
                                                                    <i class="fas fa-check"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <p class="mb-0 text-body-emphasis">No organisations found</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>


                    @if(auth()->user()->hasRole('super-admin'))
                    <li class="nav-item">
                        <a href="{{route('admin.organisations.manage')}}" class="nav-link" title="Admin Dashboard">
                            <span class="d-none d-sm-inline-block me-1">Admin</span>
                            <i class="fas fa-user-shield"></i>
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <div class="theme-control-toggle fa-icon-wait px-2">
                            <input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox"
                                data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" />
                            <label class="mb-0 theme-control-toggle-label theme-control-toggle-light"
                                for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left"
                                title="Switch theme"><span class="icon" data-feather="moon"></span></label>
                            <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark"
                                for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left"
                                title="Switch theme"><span class="icon" data-feather="sun"></span></label>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" style="min-width: 2.5rem" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            data-bs-auto-close="outside"><span data-feather="bell"
                                style="height:20px;width:20px;"></span></a>

                        <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border border-300 navbar-dropdown-caret"
                            id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
                            <div class="card position-relative border-0">
                                <div class="card-header p-2">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="text-black mb-0">Notificatons</h5>
                                        <button class="btn btn-link p-0 fs--1 fw-normal" type="button">Mark all as
                                            read
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="scrollbar-overlay" style="height: 27rem;">
                                        <div class="border-300">
                                            <div
                                                class="px-2 px-sm-3 py-3 border-300 notification-card position-relative read border-bottom">
                                                <div
                                                    class="d-flex align-items-center justify-content-between position-relative">
                                                    <div class="d-flex">
                                                        <div class="avatar avatar-m status-online me-3"><img
                                                                class="rounded-circle"
                                                                src="assets/img/team/40x40/30.webp" alt="" />
                                                        </div>
                                                        <div class="flex-1 me-sm-3">
                                                            <h4 class="fs--1 text-black"> Notification test</h4>
                                                            <p class="fs--1 text-1000 mb-2 mb-sm-3 fw-normal"><span
                                                                    class='me-1 fs--2'>💬</span>Notification
                                                                message.<span
                                                                    class="ms-2 text-400 fw-bold fs--2">10m</span></p>
                                                            <p class="text-800 fs--1 mb-0"><span
                                                                    class="me-1 fas fa-clock"></span><span
                                                                    class="fw-bold">10:41 AM </span>date
                                                                comes here</p>
                                                        </div>
                                                    </div>
                                                    <div class="font-sans-serif d-none d-sm-block">
                                                        <button
                                                            class="btn fs--2 btn-sm dropdown-toggle dropdown-caret-none transition-none notification-dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown"
                                                            data-boundary="window" aria-haspopup="true"
                                                            aria-expanded="false" data-bs-reference="parent"><span
                                                                class="fas fa-ellipsis-h fs--2 text-900"></span></button>
                                                        <div class="dropdown-menu dropdown-menu-end py-2"><a
                                                                class="dropdown-item" href="#!">Mark as
                                                                unread</a></div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                                <div class="card-footer p-0 border-top border-0">
                                    <div class="my-2 text-center fw-bold fs--2 text-600"><a class="fw-bolder"
                                            href="pages/notifications.html">Notification
                                            history</a></div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0" id="navbarDropdownUser"
                            href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                            aria-haspopup="true" aria-expanded="false">
                            <div class="avatar avatar-l ">
                                <img class="rounded-circle " src="assets/img/team/40x40/57.webp" alt="" />

                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border border-300"
                            aria-labelledby="navbarDropdownUser">
                            <div class="card position-relative border-0">
                                <div class="card-body p-0">
                                    <div class="text-center pt-4 pb-3">
                                        <div class="avatar avatar-xl ">
                                            <img class="rounded-circle " src="" alt="" />

                                        </div>
                                        <h6 class="mt-2 text-black">Admin</h6>
                                    </div>
                                    <div class="mb-3 mx-3">
                                        <input class="form-control form-control-sm" id="statusUpdateInput"
                                            type="text" placeholder="Update your status" />
                                    </div>
                                </div>
                                <div class="overflow-auto scrollbar" style="height: 10rem;">
                                    <ul class="nav d-flex flex-column mb-2 pb-1">
                                        <li class="nav-item"><a class="nav-link px-3" href="#!"> <span
                                                    class="me-2 text-900"
                                                    data-feather="user"></span><span>Profile</span></a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link px-3" href="#!"><span
                                                    class="me-2 text-900"
                                                    data-feather="pie-chart"></span>Dashboard</a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link px-3" href="#!"> <span
                                                    class="me-2 text-900" data-feather="settings"></span>Settings
                                                &amp; Privacy </a></li>
                                        <li class="nav-item"><a class="nav-link px-3" href="#!"> <span
                                                    class="me-2 text-900" data-feather="help-circle"></span>Help
                                                Center</a></li>
                                    </ul>
                                </div>
                                <div class="card-footer p-0 border-top">
                                    <hr />
                                    <div class="px-3">
                                        <form method="POST" action="{{ route('logout') }}" class="w-100">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-phoenix-secondary d-flex flex-center w-100">
                                                <span class="me-2" data-feather="log-out"></span>Sign out
                                            </button>
                                        </form>
                                    </div>
                                    <div class="my-2 text-center fw-bold fs--2 text-600">
                                        <a class="text-600 me-1" href="#!">Privacy policy</a>&bull;
                                        <a class="text-600 mx-1" href="#!">Terms</a>&bull;
                                        <a class="text-600 ms-1" href="#!">Cookies</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>


        <div class="content">
            @yield('content')
            <footer class="footer position-absolute">
                <div class="row g-0 justify-content-between align-items-center h-100">
                    <div class="col-12 col-sm-auto text-center">
                        <p class="mb-0 mt-2 mt-sm-0 text-900">Regional CBNRM<span
                                class="d-none d-sm-inline-block"></span><span
                                class="d-none d-sm-inline-block mx-1">|</span><br
                                class="d-sm-none" />{{ date('Y') }} &copy;<a class="mx-1"
                                href="https://leadingdigital.africa">Developed
                                By Leading Digital</a></p>
                    </div>
                    <div class="col-12 col-sm-auto text-center">
                        <p class="mb-0 text-600">v1.1.0</p>
                    </div>
                </div>
            </footer>
        </div>

        <script>
            var navbarTopStyle = window.config.config.phoenixNavbarTopStyle;
            var navbarTop = document.querySelector('.navbar-top');
            if (navbarTopStyle === 'darker') {
                navbarTop.classList.add('navbar-darker');
            }

            var navbarVerticalStyle = window.config.config.phoenixNavbarVerticalStyle;
            var navbarVertical = document.querySelector('.navbar-vertical');
            if (navbarVertical && navbarVerticalStyle === 'darker') {
                navbarVertical.classList.add('navbar-darker');
            }
        </script>

    </main>
    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->


    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src="{{ asset('/backend/vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('/backend/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/backend/vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('/backend/vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('/backend/vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('/backend/vendors/lodash/lodash.min.js') }}"></script>
    <!-- For external assets, you don't need the asset helper -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="{{ asset('/backend/vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('/backend/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('/backend/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/phoenix.js') }}"></script>
    <script src="{{ asset('/backend/vendors/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('/backend/vendors/leaflet/leaflet.js') }}"></script>
    <script src="{{ asset('/backend/vendors/leaflet.markercluster/leaflet.markercluster.js') }}"></script>
    <script src="{{ asset('/backend/vendors/leaflet.tilelayer.colorfilter/leaflet-tilelayer-colorfilter.min.js') }}">
    </script>
      <!-- Scripts -->
      <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

   
    <!-- Add Flatpickr JS -->
    <script src="{{ asset('vendors/flatpickr/flatpickr.min.js') }}"></script>

    <!-- jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
     
    <script src="{{ asset('backend/assets/js/ecommerce-dashboard.js') }}"></script>
    
   

    <script>
        $(document).ready(function() {
            // Find all active nav-links and iterate over them
            $('.nav-link.active').each(function() {
                // Find the closest collapsible parent
                var parentCollapse = $(this).closest('.collapse');

                if (parentCollapse.length) {
                    // Show the parent collapse
                    parentCollapse.addClass('show');

                    // Find the parent nav-link that controls the collapse
                    var parentNavLink = parentCollapse.prev('.nav-link');

                    // Set aria-expanded to true and change the caret direction
                    parentNavLink.attr('aria-expanded', 'true')
                        .find('.dropdown-indicator-icon .fas')
                        .removeClass('fa-caret-right')
                        .addClass('fa-caret-down');
                }
            });

            // Ensure that only one collapsible is open at a time
            $('.collapse').on('show.bs.collapse', function() {
                var actives = $(this).closest('.navbar-nav').find('.show');
                var hasData;

                if (actives && actives.length) {
                    hasData = actives.data('bs.collapse');
                    if (hasData && hasData.transitioning) return;
                    actives.collapse('hide').removeClass('show');
                }
            });
        });
    </script>

    @stack('scripts')

</body>

</html>
