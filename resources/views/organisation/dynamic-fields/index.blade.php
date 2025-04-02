@extends('layouts.organisation')

@section('styles')
<style>
    .card-header {
        background-color: #2d5a27 !important;
        color: white !important;
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
    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
    }
    .table > tbody > tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Dynamic Fields</h1>
                <a href="{{ route('organisation.dynamic-fields.create', $organisation->slug) }}" 
                   class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Field
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-th-list me-2"></i>Custom Fields List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                        <td class="text-end">
                                            <a href="{{ route('organisation.dynamic-fields.show', ['organisation' => $organisation->slug, 'dynamicField' => $field->id]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('organisation.dynamic-fields.edit', ['organisation' => $organisation->slug, 'dynamicField' => $field->id]) }}" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('organisation.dynamic-fields.destroy', ['organisation' => $organisation->slug, 'dynamicField' => $field->id]) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this field? This will also delete any data stored in this field.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-info-circle text-muted fa-2x mb-3"></i>
                                            <h5 class="text-muted">No Dynamic Fields</h5>
                                            <p class="text-muted mb-3">You haven't created any dynamic fields yet.</p>
                                            <a href="{{ route('organisation.dynamic-fields.create', $organisation->slug) }}" 
                                               class="btn btn-primary">
                                                Create First Field
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5><i class="fas fa-info-circle me-2"></i>About Dynamic Fields</h5>
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
    </div>
</div>
@endsection 