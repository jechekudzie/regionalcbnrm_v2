<?php

namespace App\Models\Organisation;

use App\Models\Admin\ControlMeasure;
use App\Models\Admin\Organisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProblemAnimalControl extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'wildlife_conflict_incident_id',
        'control_date',
        'control_time',
        'period',
        'location',
        'description',
        'latitude',
        'longitude',
        'estimated_number',
    ];

    protected $casts = [
        'control_date' => 'date',
        'control_time' => 'datetime',
        'period' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'estimated_number' => 'integer',
    ];

    /**
     * Get the organisation that owns the problem animal control record.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the wildlife conflict incident associated with this control.
     */
    public function wildlifeConflictIncident()
    {
        return $this->belongsTo(WildlifeConflictIncident::class);
    }

    /**
     * Get the control measures associated with this control.
     */
    public function controlMeasures()
    {
        return $this->belongsToMany(ControlMeasure::class, 'pac_control_measures')
                    ->withTimestamps();
    }

    /**
     * Get the species information through the wildlife conflict incident.
     */
    public function species()
    {
        return $this->wildlifeConflictIncident->species();
    }
}
