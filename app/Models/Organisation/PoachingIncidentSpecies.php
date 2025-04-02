<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoachingIncidentSpecies extends Model
{
    use HasFactory;

    protected $fillable = [
        'poaching_incident_id',
        'species_id',
        'estimate_number',
    ];

    public function poachingIncident(): BelongsTo
    {
        return $this->belongsTo(PoachingIncident::class);
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }
}
