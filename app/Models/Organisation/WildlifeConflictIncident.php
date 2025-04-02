<?php

namespace App\Models\Organisation;

use App\Models\Admin\ConflictType;
use App\Models\Admin\Organisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DynamicFieldValue;

class WildlifeConflictIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'title',
        'period',
        'incident_date',
        'incident_time',
        'longitude',
        'latitude',
        'location_description',
        'description',
        'conflict_type_id'
    ];

    protected $casts = [
        'incident_date' => 'date',
        'incident_time' => 'datetime',
        'period' => 'integer',
        'longitude' => 'decimal:7',
        'latitude' => 'decimal:7'
    ];

    /**
     * Get the organisation that owns the incident.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the conflict type associated with the incident.
     */
    public function conflictType()
    {
        return $this->belongsTo(ConflictType::class);
    }

    /**
     * Get the dynamic fields associated with the incident.
     */
    public function dynamicFields()
    {
        return $this->hasMany(DynamicField::class);
    }

    /**
     * Get the species involved in the incident.
     */
    public function species()
    {
        return $this->belongsToMany(Species::class, 'wildlife_conflict_incident_species')
                    ->withTimestamps();
    }

    /**
     * Get the conflict outcomes for this incident.
     */
    public function outcomes()
    {
        return $this->hasMany(WildlifeConflictOutcome::class);
    }

    /**
     * Get the dynamic field values for this incident.
     */
    public function dynamicFieldValues()
    {
        return $this->hasMany(DynamicFieldValue::class);
    }

    /**
     * Get the problem animal control records related to this incident.
     */
    public function problemAnimalControls()
    {
        return $this->hasMany(ProblemAnimalControl::class);
    }
} 