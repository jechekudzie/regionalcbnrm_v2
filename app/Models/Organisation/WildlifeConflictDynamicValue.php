<?php

namespace App\Models\Organisation;

use App\Models\DynamicField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WildlifeConflictDynamicValue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wildlife_conflict_outcome_id',
        'dynamic_field_id',
        'field_value',
    ];

    /**
     * Get the wildlife conflict outcome this value belongs to.
     */
    public function wildlifeConflictOutcome()
    {
        return $this->belongsTo(WildlifeConflictOutcome::class);
    }

    /**
     * Get the dynamic field this value belongs to.
     */
    public function dynamicField()
    {
        return $this->belongsTo(DynamicField::class);
    }
} 