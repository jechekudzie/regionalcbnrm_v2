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
                <h1 class="h3 mb-0">Dynamic Fields</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard.index', $organisation->slug) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dynamic Fields</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('organisation.dynamic-fields.create', $organisation->slug) }}" 
                   class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Add New Field
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
                <i class="fas fa-th-list me-1"></i>
                Custom Fields List
            </div>
        </div>
        <div class="card-body">
            <div class="datatables-info-box">
                <i class="fas fa-info-circle me-1"></i>
                This table displays all custom fields. Use the search and filters to narrow down results.
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="dynamicFieldsTable">
                    <thead>
                        <tr>
                            <th>Field Name</th>
                            <th>Label</th>
                            <th>Type</th>
                            <th>Conflict Outcome</th>
                            <th>Options</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dynamicFields as $field)
                            <tr>
                                <td>{{ $field->field_name }}</td>
                                <td>{{ $field->label }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($field->field_type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($field->conflictOutcome)
                                        <span class="badge bg-success">
                                            {{ $field->conflictOutcome->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($field->field_type, ['select', 'checkbox', 'radio']))
                                        <span class="badge bg-info text-dark">
                                            {{ $field->options->count() }} options
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-end table-actions">
                                    <a href="{{ route('organisation.dynamic-fields.show', ['organisation' => $organisation->slug, 'dynamicField' => $field->id]) }}" 
                                       class="btn btn-sm btn-outline-primary btn-action" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('organisation.dynamic-fields.edit', ['organisation' => $organisation->slug, 'dynamicField' => $field->id]) }}" 
                                       class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('organisation.dynamic-fields.destroy', ['organisation' => $organisation->slug, 'dynamicField' => $field->id]) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger btn-action" 
                                                onclick="return confirm('Are you sure you want to delete this field? This will also delete any data stored in this field.')"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-th-list"></i>
                                        <h5>No Dynamic Fields</h5>
                                        <p>You haven't created any dynamic fields yet. Start by adding your first field.</p>
                                        <a href="{{ route('organisation.dynamic-fields.create', $organisation->slug) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i> Create First Field
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

    <div class="card mt-4">
        <div class="card-header">
            <div>
                <i class="fas fa-info-circle me-1"></i>
                About Dynamic Fields
            </div>
        </div>
        <div class="card-body">
            <p>
                Dynamic fields allow you to customize and extend the data you collect for wildlife conflicts.
                You can create different types of fields such as text fields, dropdowns, checkboxes, and more.
            </p>
            <ul>
                <li><strong>Text Field:</strong> For collecting short text input</li>
                <li><strong>Text Area:</strong> For collecting longer text descriptions</li>
                <li><strong>Number:</strong> For collecting numeric data</li>
                <li><strong>Date:</strong> For collecting date information</li>
                <li><strong>Dropdown:</strong> For selecting from a list of options</li>
                <li><strong>Checkbox:</strong> For selecting multiple options</li>
                <li><strong>Radio Button:</strong> For selecting a single option from multiple choices</li>
            </ul>
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
        $('#dynamicFieldsTable').DataTable({
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
            "order": [[0, 'asc']],
            "columnDefs": [
                { "orderable": false, "targets": 5 }
            ]
        });
    });
</script>
@endpush 