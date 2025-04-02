<?php

namespace App\Models\Organisation;

use App\Models\Admin\OffenceType;
use App\Models\Admin\Organisation;
use App\Models\Admin\PoacherType;
use App\Models\Admin\PoachingMethod;
use App\Models\Admin\PoachingReason;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoachingIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'title',
        'location',
        'longitude',
        'latitude',
        'docket_number',
        'docket_status',
        'period',
        'date',
        'time'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'longitude' => 'decimal:7',
        'latitude' => 'decimal:7',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'poaching_incident_species')
            ->withPivot('estimate_number')
            ->withTimestamps();
    }

    public function methods(): BelongsToMany
    {
        return $this->belongsToMany(PoachingMethod::class, 'poaching_incident_methods')
            ->withTimestamps();
    }

    public function poachers(): HasMany
    {
        return $this->hasMany(Poacher::class);
    }
}
