<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organisation;
use App\Models\DynamicField;
use App\Models\DynamicFieldOption;
use App\Models\ConflictOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DynamicFieldController extends Controller
{
    /**
     * Display a listing of dynamic fields.
     */
    public function index(Organisation $organisation)
    {
        $dynamicFields = DynamicField::where('organisation_id', $organisation->id)
            ->with('conflictOutcome')
            ->orderBy('field_name')
            ->get();

        return view('organisation.dynamic-fields.index', compact('organisation', 'dynamicFields'));
    }

    /**
     * Show the form for creating a new dynamic field.
     */
    public function create(Organisation $organisation)
    {
        $fieldTypes = [
            'text' => 'Text Field',
            'textarea' => 'Text Area',
            'number' => 'Number',
            'date' => 'Date',
            'select' => 'Dropdown',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Button'
        ];
        
        $conflictOutcomes = ConflictOutcome::orderBy('name')->get();
        
        return view('organisation.dynamic-fields.create', compact('organisation', 'fieldTypes', 'conflictOutcomes'));
    }

    /**
     * Store a newly created dynamic field in storage.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,textarea,number,date,select,checkbox,radio',
            'label' => 'required|string|max:255',
            'conflict_outcome_id' => 'nullable|exists:conflict_out_comes,id',
            'options' => 'required_if:field_type,select,checkbox,radio|array|nullable',
            'options.*.option_value' => 'required_with:options|string|max:255',
            'options.*.option_label' => 'required_with:options|string|max:255',
        ]);

        $dynamicField = DynamicField::create([
            'organisation_id' => $organisation->id,
            'conflict_outcome_id' => $validated['conflict_outcome_id'] ?? null,
            'field_name' => $validated['field_name'],
            'field_type' => $validated['field_type'],
            'label' => $validated['label'],
            'slug' => Str::slug($validated['field_name']),
        ]);

        // Store options if applicable
        if (in_array($validated['field_type'], ['select', 'checkbox', 'radio']) && !empty($validated['options'])) {
            foreach ($validated['options'] as $option) {
                DynamicFieldOption::create([
                    'dynamic_field_id' => $dynamicField->id,
                    'option_value' => $option['option_value'],
                    'option_label' => $option['option_label'],
                ]);
            }
        }

        return redirect()
            ->route('organisation.dynamic-fields.create', $organisation->slug)
            ->with('success', 'Dynamic field created successfully.');
    }

    /**
     * Display the specified dynamic field.
     */
    public function show(Organisation $organisation, DynamicField $dynamicField)
    {
        $dynamicField->load(['options', 'conflictOutcome']);
        
        return view('organisation.dynamic-fields.show', [
            'organisation' => $organisation,
            'dynamicField' => $dynamicField
        ]);
    }

    /**
     * Show the form for editing the specified dynamic field.
     */
    public function edit(Organisation $organisation, DynamicField $dynamicField)
    {
        $fieldTypes = [
            'text' => 'Text Field',
            'textarea' => 'Text Area',
            'number' => 'Number',
            'date' => 'Date',
            'select' => 'Dropdown',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Button'
        ];
        
        $conflictOutcomes = ConflictOutcome::orderBy('name')->get();
        $dynamicField->load('options');
        
        return view('organisation.dynamic-fields.edit', compact('organisation', 'dynamicField', 'fieldTypes', 'conflictOutcomes'));
    }

    /**
     * Update the specified dynamic field in storage.
     */
    public function update(Request $request, Organisation $organisation, DynamicField $dynamicField)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,textarea,number,date,select,checkbox,radio',
            'label' => 'required|string|max:255',
            'conflict_outcome_id' => 'nullable|exists:conflict_out_comes,id',
            'options' => 'required_if:field_type,select,checkbox,radio|array|nullable',
            'options.*.id' => 'nullable|exists:dynamic_field_options,id',
            'options.*.option_value' => 'required_with:options|string|max:255',
            'options.*.option_label' => 'required_with:options|string|max:255',
        ]);

        $dynamicField->update([
            'field_name' => $validated['field_name'],
            'field_type' => $validated['field_type'],
            'label' => $validated['label'],
            'conflict_outcome_id' => $validated['conflict_outcome_id'] ?? null,
            'slug' => Str::slug($validated['field_name']),
        ]);

        // Update options if applicable
        if (in_array($validated['field_type'], ['select', 'checkbox', 'radio']) && !empty($validated['options'])) {
            // Get existing option IDs
            $existingOptionIds = $dynamicField->options->pluck('id')->toArray();
            $updatedOptionIds = [];
            
            foreach ($validated['options'] as $option) {
                if (isset($option['id'])) {
                    // Update existing option
                    DynamicFieldOption::find($option['id'])->update([
                        'option_value' => $option['option_value'],
                        'option_label' => $option['option_label'],
                    ]);
                    $updatedOptionIds[] = $option['id'];
                } else {
                    // Create new option
                    $newOption = DynamicFieldOption::create([
                        'dynamic_field_id' => $dynamicField->id,
                        'option_value' => $option['option_value'],
                        'option_label' => $option['option_label'],
                    ]);
                    $updatedOptionIds[] = $newOption->id;
                }
            }
            
            // Delete options that were not included in the update
            $optionsToDelete = array_diff($existingOptionIds, $updatedOptionIds);
            if (!empty($optionsToDelete)) {
                DynamicFieldOption::whereIn('id', $optionsToDelete)->delete();
            }
        } else {
            // Delete all options if field type no longer needs them
            $dynamicField->options()->delete();
        }

        return redirect()
            ->route('organisation.dynamic-fields.index', $organisation->slug)
            ->with('success', 'Dynamic field updated successfully.');
    }

    /**
     * Remove the specified dynamic field from storage.
     */
    public function destroy(Organisation $organisation, DynamicField $dynamicField)
    {
        // Delete associated options first
        $dynamicField->options()->delete();
        
        // Then delete the field
        $dynamicField->delete();

        return redirect()
            ->route('organisation.dynamic-fields.index', $organisation->slug)
            ->with('success', 'Dynamic field deleted successfully.');
    }
} 