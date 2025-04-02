<?php

namespace App\Models\Organisation;

use App\Models\Admin\ConflictOutCome;
use App\Models\DynamicField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WildlifeConflictOutcome extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wildlife_conflict_incident_id',
        'conflict_out_come_id',
    ];

    /**
     * Get the wildlife conflict incident this outcome belongs to.
     */
    public function wildlifeConflictIncident()
    {
        return $this->belongsTo(WildlifeConflictIncident::class);
    }

    /**
     * Get the conflict outcome type.
     */
    public function conflictOutCome()
    {
        return $this->belongsTo(ConflictOutCome::class);
    }

    /**
     * Get the dynamic field values for this outcome.
     */
    public function dynamicValues()
    {
        return $this->hasMany(WildlifeConflictDynamicValue::class);
    }

    /**
     * Get all dynamic fields associated with this conflict outcome type.
     */
    public function getDynamicFields()
    {
        return DynamicField::where('conflict_outcome_id', $this->conflict_out_come_id)->get();
    }
} 