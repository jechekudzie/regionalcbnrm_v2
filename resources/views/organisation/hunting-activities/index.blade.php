@extends('layouts.organisation')

@section('title', 'Hunting Activities')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #2E7D33;
            --primary-light: #E8F5E9;
            --primary-dark: #1B5E20;
            --text-dark: #344050;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
        }

        .page-header {
            background: linear-gradient(to right, var(--primary-light), #ffffff);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(46, 125, 51, 0.1);
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .page-title i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .breadcrumb {
            margin-bottom: 0;
            background: transparent;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .breadcrumb-item.active {
            color: var(--text-muted);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04);
            background: #ffffff;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: var(--primary-color);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .card-header i {
            font-size: 1.1rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .table {
            margin-bottom: 0;
            width: 100%;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid var(--primary-light);
            color: var(--text-dark);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 1rem;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: var(--text-dark);
            border-bottom: 1px solid var(--border-color);
        }

        .badge {
            padding: 0.5em 1em;
            font-weight: 500;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        .badge.bg-primary {
            background-color: var(--primary-color) !important;
        }

        .badge.bg-success {
            background-color: var(--primary-color) !important;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .btn-group {
            gap: 0.25rem;
        }

        .btn-group .btn {
            padding: 0.5rem;
            font-size: 0.9rem;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            transition: all 0.2s;
        }

        .btn-group .btn i {
            font-size: 0.9rem;
        }

        .btn-info {
            background-color: #0dcaf0;
            border-color: #0dcaf0;
            color: #fff;
        }

        .btn-info:hover {
            background-color: #31d2f2;
            border-color: #25cff2;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }

        .btn-warning:hover {
            background-color: #ffca2c;
            border-color: #ffc720;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #bb2d3b;
            border-color: #b02a37;
        }

        .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        .btn-success:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-outline-success {
            color: var(--primary-color);
            border-color: var(--primary-color);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        .btn-outline-success:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 1.25rem;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: var(--primary-light);
            border-color: var(--primary-color);
            color: var(--primary-dark);
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.5rem 1rem;
            margin-left: 0.5rem;
        }

        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.5rem 2rem 0.5rem 1rem;
            margin: 0 0.5rem;
        }

        .dt-buttons {
            margin-bottom: 1rem;
            gap: 0.5rem;
            display: inline-flex;
        }

        .dt-button {
            background-color: #fff !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 6px !important;
            color: var(--text-dark) !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            transition: all 0.2s !important;
        }

        .dt-button:hover {
            background-color: var(--primary-light) !important;
            border-color: var(--primary-color) !important;
            color: var(--primary-color) !important;
        }

        .dataTables_info {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        .dataTables_paginate {
            margin-top: 1rem;
        }

        .paginate_button {
            padding: 0.5rem 1rem !important;
            border-radius: 6px !important;
            margin: 0 0.25rem !important;
        }

        .paginate_button.current {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-hiking"></i>
                    Hunting Activities
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('organisation.dashboard.index', $organisation->slug) }}">
                                <i class="fas fa-home"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Hunting Activities</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-success" onclick="window.location.reload();">
                    <i class="fas fa-sync"></i>
                    Refresh
                </button>
                <a href="{{ route('organisation.hunting-activities.create', $organisation->slug ?? '') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    Add New Activity
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i>
            Hunting Activities List
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="huntingActivitiesTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Concession</th>
                            <th>Safari Operator</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Species Count</th>
                            <th>Total Off-take</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $activity)
                            <tr>
                                <td>{{ $activity->huntingConcession->name }}</td>
                                <td>{{ $activity->safariOperator->name ?? 'Not assigned' }}</td>
                                <td>{{ $activity->start_date->format('d M Y') }}</td>
                                <td>{{ $activity->end_date?->format('d M Y') ?? 'Ongoing' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $activity->species->count() }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $activity->species->sum('pivot.off_take') }}</span>
                                </td>
                                <td class="text-center">
                                    @if(!$activity->end_date)
                                        <span class="badge bg-warning">Ongoing</span>
                                    @else
                                        <span class="badge bg-success">Completed</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('organisation.hunting-activities.show', [$organisation->slug ?? '', $activity]) }}" 
                                           class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('organisation.hunting-activities.edit', [$organisation->slug ?? '', $activity]) }}" 
                                           class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('organisation.hunting-activities.destroy', [$organisation->slug ?? '', $activity]) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this activity?')" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <script>
        $(document).ready(function() {
            $('#huntingActivitiesTable').DataTable({
                responsive: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print'
                    }
                ],
                order: [[2, 'desc']], // Sort by start date
                pageLength: 10,
                language: {
                    search: "Search activities:",
                    lengthMenu: "Show _MENU_ entries per page",
                    zeroRecords: "No matching activities found",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)"
                }
            });
        });
    </script>
@endpush
@endsection 