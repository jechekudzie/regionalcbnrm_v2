@extends('layouts.organisation')

@push('head')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<style>
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border: none;
        margin-bottom: 1.5rem;
    }
    .card-header {
        background-color: #2d5a27 !important;
        color: white !important;
        border-radius: 0.5rem 0.5rem 0 0 !important;
        padding: 1rem 1.25rem;
    }
    .btn-primary, .btn-success {
        background-color: #2d5a27 !important;
        border-color: #2d5a27 !important;
    }
    .btn-primary:hover, .btn-success:hover {
        background-color: #1e3d1a !important;
        border-color: #1e3d1a !important;
    }
    .badge-success {
        background-color: #2d5a27 !important;
        color: white !important;
    }
    .badge-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    .badge {
        font-size: 0.75em;
        padding: 0.35em 0.65em;
        font-weight: normal;
    }
    .table > :not(caption) > * > * {
        padding: 0.75rem;
        vertical-align: middle;
    }
    .dataTables_wrapper .row {
        margin: 0;
        align-items: center;
    }
    .dataTables_filter, .dataTables_length {
        margin-bottom: 1rem;
    }
    .dataTables_length select, .dataTables_filter input {
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .dataTables_info {
        margin-top: 0.5rem;
    }
    .table-actions {
        white-space: nowrap;
    }
    .btn-action {
        padding: 0.25rem 0.5rem;
        margin-right: 0.25rem;
    }
    .btn-action i {
        font-size: 0.875rem;
    }
    .dt-buttons {
        margin-bottom: 1rem;
    }
    .page-header {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .page-header h1 {
        margin-bottom: 0.5rem;
        color: #2d5a27;
    }
    .page-header .breadcrumb {
        margin-bottom: 0;
        background: transparent;
        padding: 0;
    }
    .page-header .breadcrumb-item a {
        color: #2d5a27;
    }
    .datatables-info-box {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        padding: 0.75rem;
        margin-bottom: 1rem;
        border-left: 4px solid #2d5a27;
    }
    .dt-buttons .btn {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #212529;
    }
    .dt-buttons .btn:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    .dataTables_length label, .dataTables_filter label {
        font-weight: 500;
    }
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    .empty-state i {
        font-size: 3rem;
        color: #adb5bd;
        margin-bottom: 1rem;
    }
    .empty-state h5 {
        color: #495057;
        margin-bottom: 1rem;
    }
    .empty-state p {
        color: #6c757d;
        margin-bottom: 1.5rem;
        max-width: 30rem;
        margin-left: auto;
        margin-right: auto;
    }

    /* DataTables Custom styling */
    div.dataTables_wrapper div.dataTables_length select {
        width: auto;
        display: inline-block;
    }
    div.dataTables_wrapper div.dataTables_filter {
        text-align: right;
    }
    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 0.5em;
        display: inline-block;
        width: auto;
    }
    div.dataTables_wrapper div.dataTables_info {
        padding-top: 0.85em;
    }
    div.dataTables_wrapper div.dataTables_paginate {
        margin: 0;
        white-space: nowrap;
        text-align: right;
    }
    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        margin: 2px 0;
        white-space: nowrap;
        justify-content: flex-end;
    }
    .page-item.active .page-link {
        background-color: #2d5a27;
        border-color: #2d5a27;
    }
    .page-link {
        color: #2d5a27;
    }
    .page-link:hover {
        color: #1e3d1a;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Wildlife Conflict Incidents</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard.index', $organisation->slug) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Wildlife Conflicts</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('organisation.wildlife-conflicts.create', $organisation->slug) }}" 
                   class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Record New Incident
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-paw me-1"></i>
                Wildlife Conflict Incidents
            </div>
        </div>
        <div class="card-body">
            <div class="datatables-info-box">
                <i class="fas fa-info-circle me-1"></i>
                This table displays all wildlife conflict incidents. Use the search and filters to narrow down results.
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="wildlifeConflictsTable">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Conflict Type</th>
                            <th>Species</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wildlifeConflictIncidents as $wildlifeConflictIncident)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $wildlifeConflictIncident->incident_date->format('d M Y') }}</div>
                                    <div class="text-muted small">{{ $wildlifeConflictIncident->incident_time->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div>{{ $wildlifeConflictIncident->location_description }}</div>
                                    @if($wildlifeConflictIncident->latitude && $wildlifeConflictIncident->longitude)
                                        <div class="text-muted small">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ number_format($wildlifeConflictIncident->latitude, 6) }}, 
                                            {{ number_format($wildlifeConflictIncident->longitude, 6) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        {{ $wildlifeConflictIncident->conflictType->name }}
                                    </span>
                                </td>
                                <td>
                                    @foreach($wildlifeConflictIncident->species->take(2) as $species)
                                        <span class="badge bg-success">{{ $species->name }}</span>
                                    @endforeach
                                    @if($wildlifeConflictIncident->species->count() > 2)
                                        <span class="badge bg-secondary">+{{ $wildlifeConflictIncident->species->count() - 2 }}</span>
                                    @endif
                                </td>
                                <td class="text-end table-actions">
                                    <a href="{{ route('organisation.wildlife-conflicts.show', ['organisation' => $organisation->slug, 'wildlifeConflictIncident' => $wildlifeConflictIncident]) }}" 
                                       class="btn btn-sm btn-outline-primary btn-action" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('organisation.wildlife-conflicts.edit', ['organisation' => $organisation->slug, 'wildlifeConflictIncident' => $wildlifeConflictIncident]) }}" 
                                       class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('organisation.wildlife-conflicts.destroy', ['organisation' => $organisation->slug, 'wildlifeConflictIncident' => $wildlifeConflictIncident]) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger btn-action" 
                                                onclick="return confirm('Are you sure you want to delete this incident?')"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-paw"></i>
                                        <h5>No Wildlife Conflict Incidents</h5>
                                        <p>No wildlife conflict incidents have been recorded yet. Start by adding your first incident.</p>
                                        <a href="{{ route('organisation.wildlife-conflicts.create', $organisation->slug) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i> Record First Incident
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script>
    $(document).ready(function() {
        $('#wildlifeConflictsTable').DataTable({
            responsive: true,
            autoWidth: false,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'excel', 'pdf', 'print'
            ],
            "paging": true,
            "ordering": true,
            "info": true,
            "searching": true,
            "lengthChange": true,
            "pageLength": 10,
            "order": [[0, 'desc']],
            "columnDefs": [
                { "orderable": false, "targets": 4 }
            ]
        });
    });
</script>
@endpush 