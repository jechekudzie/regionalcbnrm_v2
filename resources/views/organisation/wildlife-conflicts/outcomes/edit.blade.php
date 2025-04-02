@extends('layouts.organisation')

@section('title', 'Edit Conflict Outcome')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4 border-bottom pb-2">
        <div class="row">
            <div class="col-md-8">
                <h1>Edit Conflict Outcome</h1>
                <p class="text-muted">
                    <i class="fas fa-info-circle"></i> Editing outcome for incident: <strong>{{ $wildlifeConflictIncident->title }}</strong>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('organisation.wildlife-conflicts.show', [$organisation->slug, $wildlifeConflictIncident->id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Incident
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-clipboard-list me-2"></i>Outcome Details: {{ $outcome->conflictOutCome->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('organisation.wildlife-conflicts.outcomes.update', [$organisation->slug, $wildlifeConflictIncident->id, $outcome->id]) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        @if($dynamicFields->count() > 0)
                            <div class="mb-4">
                                <h5>Additional Details</h5>
                                
                                @foreach($dynamicFields as $field)
                                    <div class="mb-3">
                                        <label for="dynamic_field_{{ $field->id }}" class="form-label">{{ $field->label }}</label>
                                        
                                        @switch($field->field_type)
                                            @case('text')
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="dynamic_field_{{ $field->id }}" 
                                                       name="dynamic_field_{{ $field->id }}" 
                                                       value="{{ $dynamicValues[$field->id] ?? '' }}">
                                                @break
                                            
                                            @case('number')
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="dynamic_field_{{ $field->id }}" 
                                                       name="dynamic_field_{{ $field->id }}" 
                                                       value="{{ $dynamicValues[$field->id] ?? '' }}">
                                                @break
                                            
                                            @case('textarea')
                                                <textarea class="form-control" 
                                                          id="dynamic_field_{{ $field->id }}" 
                                                          name="dynamic_field_{{ $field->id }}" 
                                                          rows="3">{{ $dynamicValues[$field->id] ?? '' }}</textarea>
                                                @break
                                            
                                            @case('select')
                                                <select class="form-select" 
                                                        id="dynamic_field_{{ $field->id }}" 
                                                        name="dynamic_field_{{ $field->id }}">
                                                    <option value="">-- Select Option --</option>
                                                    @foreach($field->options as $option)
                                                        <option value="{{ $option->value }}" {{ ($dynamicValues[$field->id] ?? '') == $option->value ? 'selected' : '' }}>
                                                            {{ $option->label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @break
                                            
                                            @default
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="dynamic_field_{{ $field->id }}" 
                                                       name="dynamic_field_{{ $field->id }}" 
                                                       value="{{ $dynamicValues[$field->id] ?? '' }}">
                                        @endswitch
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                No dynamic fields are associated with this outcome type.
                            </div>
                        @endif

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Update Outcome
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 