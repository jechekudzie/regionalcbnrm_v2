<?php

namespace App\Models\Organisation;

use App\Models\Admin\PoachingMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoachingIncidentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'poaching_incident_id',
        'poaching_method_id',
    ];

    public function poachingIncident(): BelongsTo
    {
        return $this->belongsTo(PoachingIncident::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(PoachingMethod::class, 'poaching_method_id');
    }
}
