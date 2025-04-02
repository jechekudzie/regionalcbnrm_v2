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
    .detail-label {
        font-weight: 600;
        color: #495057;
    }
    .option-item {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        padding: 1rem;
        margin-bottom: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Field Details</h1>
                <div>
                    <a href="{{ route('organisation.dynamic-fields.edit', ['organisation' => $organisation->slug, 'dynamicField' => $dynamicField->id]) }}" 
                       class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Edit Field
                    </a>
                    <a href="{{ route('organisation.dynamic-fields.index', $organisation->slug) }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>{{ $dynamicField->label }} ({{ $dynamicField->field_name }})</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="detail-label">Field Name:</p>
                            <p>{{ $dynamicField->field_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="detail-label">Label:</p>
                            <p>{{ $dynamicField->label }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="detail-label">Field Type:</p>
                            <p>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($dynamicField->field_type) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="detail-label">Conflict Outcome:</p>
                            <p>
                                @if($dynamicField->conflictOutcome)
                                    <span class="badge bg-success">
                                        {{ $dynamicField->conflictOutcome->name }}
                                    </span>
                                @else
                                    <span class="text-muted">Not associated with any outcome</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="detail-label">Slug:</p>
                            <p>{{ $dynamicField->slug }}</p>
                        </div>
                    </div>
                    
                    @if(in_array($dynamicField->field_type, ['select', 'checkbox', 'radio']))
                        <hr>
                        <h5 class="mb-3">Field Options</h5>
                        
                        @if($dynamicField->options->count() > 0)
                            <div class="row">
                                <div class="col-md-12">
                                    @foreach($dynamicField->options as $option)
                                        <div class="option-item">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="detail-label mb-1">Value:</p>
                                                    <p>{{ $option->option_value }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="detail-label mb-1">Label:</p>
                                                    <p>{{ $option->option_label }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> No options defined for this field.
                            </div>
                        @endif
                    @endif
                    
                    <div class="d-flex justify-content-end mt-4">
                        <form action="{{ route('organisation.dynamic-fields.destroy', ['organisation' => $organisation->slug, 'dynamicField' => $dynamicField->id]) }}" 
                              method="POST" 
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this field? This will also delete any data stored in this field.')">
                                <i class="fas fa-trash"></i> Delete Field
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5><i class="fas fa-lightbulb me-2 text-warning"></i>Using This Field</h5>
                    <p>
                        This dynamic field can be used in your wildlife conflict incident forms. When adding or editing a wildlife conflict incident,
                        this field will be available for data collection.
                    </p>
                    <p>
                        <strong>Field Type Information:</strong>
                        @switch($dynamicField->field_type)
                            @case('text')
                                A text field allows users to enter a single line of text.
                                @break
                            @case('textarea')
                                A text area allows users to enter multiple lines of text.
                                @break
                            @case('number')
                                A number field allows users to enter numeric values only.
                                @break
                            @case('date')
                                A date field provides a date picker for selecting dates.
                                @break
                            @case('select')
                                A dropdown field allows users to select one option from a list.
                                @break
                            @case('checkbox')
                                A checkbox field allows users to select multiple options from a list.
                                @break
                            @case('radio')
                                A radio button field allows users to select a single option from multiple choices.
                                @break
                            @default
                                This field type allows for data collection in a standard format.
                        @endswitch
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
