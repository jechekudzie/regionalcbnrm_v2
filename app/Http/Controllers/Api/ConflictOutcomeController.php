<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DynamicField;
use App\Models\ConflictOutCome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConflictOutcomeController extends Controller
{
    /**
     * Get dynamic fields for a specific conflict outcome.
     */
    public function getDynamicFields($conflictOutcome)
    {
        try {
            // Manual lookup instead of model binding
            $outcomeModel = ConflictOutCome::findOrFail($conflictOutcome);
            
            Log::info('Fetching dynamic fields for conflict outcome', [
                'outcome_id' => $outcomeModel->id,
                'outcome_name' => $outcomeModel->name
            ]);

            $fields = DynamicField::where('conflict_outcome_id', $outcomeModel->id)
                ->with('options')
                ->get();
                
            Log::info('Found dynamic fields', [
                'count' => $fields->count(),
                'field_ids' => $fields->pluck('id')
            ]);
                
            $formattedFields = $fields->map(function ($field) {
                return [
                    'id' => $field->id,
                    'label' => $field->label,
                    'field_type' => $field->field_type,
                    'options' => $field->options->map(function ($option) {
                        return [
                            'value' => $option->option_value,
                            'label' => $option->option_label
                        ];
                    })
                ];
            });
            
            return response()->json($formattedFields);
        } catch (\Exception $e) {
            Log::error('Error fetching dynamic fields', [
                'outcome_id' => $conflictOutcome,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Failed to load dynamic fields: ' . $e->getMessage()], 500);
        }
    }
} 