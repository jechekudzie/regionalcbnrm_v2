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
    .options-container {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 1rem;
        margin-top: 1rem;
    }
    .option-item {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        padding: 1rem;
        margin-bottom: 0.5rem;
        position: relative;
    }
    .remove-option {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
    }
    .add-option-row {
        margin-top: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Dynamic Field</h1>
                <a href="{{ route('organisation.dynamic-fields.index', $organisation->slug) }}" 
                   class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-edit me-2"></i>Field Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('organisation.dynamic-fields.update', ['organisation' => $organisation->slug, 'dynamicField' => $dynamicField->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="field_name" class="form-label">Field Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('field_name') is-invalid @enderror" 
                                       id="field_name" 
                                       name="field_name" 
                                       value="{{ old('field_name', $dynamicField->field_name) }}" 
                                       required>
                                <div class="form-text">Must be unique. Use only letters, numbers, and underscores.</div>
                                @error('field_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="label" class="form-label">Field Label <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('label') is-invalid @enderror" 
                                       id="label" 
                                       name="label" 
                                       value="{{ old('label', $dynamicField->label) }}" 
                                       required>
                                <div class="form-text">This is the label that will be displayed on forms.</div>
                                @error('label')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="field_type" class="form-label">Field Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('field_type') is-invalid @enderror" 
                                        id="field_type" 
                                        name="field_type" 
                                        required>
                                    <option value="">Select a field type</option>
                                    @foreach($fieldTypes as $value => $label)
                                        <option value="{{ $value }}" {{ old('field_type', $dynamicField->field_type) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('field_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="conflict_outcome_id" class="form-label">Conflict Outcome</label>
                                <select class="form-select @error('conflict_outcome_id') is-invalid @enderror" 
                                        id="conflict_outcome_id" 
                                        name="conflict_outcome_id">
                                    <option value="">-- Select Conflict Outcome (Optional) --</option>
                                    @foreach($conflictOutcomes as $outcome)
                                        <option value="{{ $outcome->id }}" {{ old('conflict_outcome_id', $dynamicField->conflict_outcome_id) == $outcome->id ? 'selected' : '' }}>
                                            {{ $outcome->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Associate this field with a specific conflict outcome.</div>
                                @error('conflict_outcome_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div id="options-section" class="mb-3" style="display: none;">
                            <label class="form-label">Field Options <span class="text-danger">*</span></label>
                            <div class="options-container">
                                <div id="options-list">
                                    <!-- Options will be added here dynamically -->
                                </div>
                                
                                <div class="add-option-row">
                                    <button type="button" id="add-option" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-plus"></i> Add Option
                                    </button>
                                </div>
                            </div>
                            @error('options')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            @error('options.*')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" 
                                    onclick="window.location='{{ route('organisation.dynamic-fields.index', $organisation->slug) }}'" 
                                    class="btn btn-outline-secondary me-2">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Update Field
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fieldTypeSelect = document.getElementById('field_type');
        const optionsSection = document.getElementById('options-section');
        const optionsList = document.getElementById('options-list');
        const addOptionBtn = document.getElementById('add-option');
        
        // Check if field type requires options
        fieldTypeSelect.addEventListener('change', function() {
            const requiresOptions = ['select', 'checkbox', 'radio'].includes(this.value);
            optionsSection.style.display = requiresOptions ? 'block' : 'none';
            
            // If switching to a field type that requires options, ensure at least one option exists
            if (requiresOptions && optionsList.children.length === 0) {
                addOption();
            }
        });
        
        // Initial check in case of form validation error and page refresh
        if (['select', 'checkbox', 'radio'].includes(fieldTypeSelect.value)) {
            optionsSection.style.display = 'block';
            
            // If options were previously entered or exist from the database, load them
            @if (old('options'))
                @foreach (old('options') as $index => $option)
                    addOption("{{ $option['option_value'] ?? '' }}", "{{ $option['option_label'] ?? '' }}", "{{ $option['id'] ?? '' }}");
                @endforeach
            @elseif ($dynamicField->options->count() > 0)
                @foreach ($dynamicField->options as $option)
                    addOption("{{ $option->option_value }}", "{{ $option->option_label }}", "{{ $option->id }}");
                @endforeach
            @else
                // Otherwise add an empty option if needed
                if (optionsList.children.length === 0) {
                    addOption();
                }
            @endif
        }
        
        // Add option button click handler
        addOptionBtn.addEventListener('click', function() {
            addOption();
        });
        
        // Function to add a new option
        function addOption(value = '', label = '', id = '') {
            const index = optionsList.children.length;
            const optionItem = document.createElement('div');
            optionItem.className = 'option-item';
            
            let idField = '';
            if (id) {
                idField = `<input type="hidden" name="options[${index}][id]" value="${id}">`;
            }
            
            optionItem.innerHTML = `
                ${idField}
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">Value</label>
                        <input type="text" 
                               class="form-control" 
                               name="options[${index}][option_value]" 
                               value="${value}" 
                               required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Label</label>
                        <input type="text" 
                               class="form-control" 
                               name="options[${index}][option_label]" 
                               value="${label}" 
                               required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger form-control remove-option">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            // Add remove button functionality
            const removeBtn = optionItem.querySelector('.remove-option');
            removeBtn.addEventListener('click', function() {
                optionItem.remove();
                // Renumber the options after removal
                renumberOptions();
            });
            
            optionsList.appendChild(optionItem);
        }
        
        // Function to renumber options after removal
        function renumberOptions() {
            const options = optionsList.querySelectorAll('.option-item');
            options.forEach((option, index) => {
                const idInput = option.querySelector('input[name^="options"][name$="[id]"]');
                const valueInput = option.querySelector('input[name^="options"][name$="[option_value]"]');
                const labelInput = option.querySelector('input[name^="options"][name$="[option_label]"]');
                
                if (idInput) {
                    idInput.name = `options[${index}][id]`;
                }
                valueInput.name = `options[${index}][option_value]`;
                labelInput.name = `options[${index}][option_label]`;
            });
        }
    });
</script>
@endpush 