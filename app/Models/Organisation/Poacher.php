<?php

namespace App\Models\Organisation;

use App\Models\Admin\City;
use App\Models\Admin\Country;
use App\Models\Admin\OffenceType;
use App\Models\Admin\PoacherType;
use App\Models\Admin\PoachingReason;
use App\Models\Admin\Province;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Poacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'poaching_incident_id',
        'first_name',
        'last_name',
        'middle_name',
        'age',
        'status',
        'country_id',
        'province_id',
        'city_id',
        'offence_type_id',
        'poacher_type_id',
        'poaching_reason_id',
    ];

    public function poachingIncident(): BelongsTo
    {
        return $this->belongsTo(PoachingIncident::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function offenceType(): BelongsTo
    {
        return $this->belongsTo(OffenceType::class);
    }

    public function poacherType(): BelongsTo
    {
        return $this->belongsTo(PoacherType::class);
    }

    public function poachingReason(): BelongsTo
    {
        return $this->belongsTo(PoachingReason::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->middle_name 
            ? "{$this->first_name} {$this->middle_name} {$this->last_name}"
            : "{$this->first_name} {$this->last_name}";
    }
}
