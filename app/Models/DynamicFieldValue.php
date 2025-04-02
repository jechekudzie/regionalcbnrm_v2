<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DynamicField;
use App\Models\Organisation\WildlifeConflictIncident;

class DynamicFieldValue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dynamic_field_id',
        'wildlife_conflict_incident_id',
        'field_value',
    ];

    /**
     * Get the dynamic field this value belongs to.
     */
    public function dynamicField()
    {
        return $this->belongsTo(DynamicField::class);
    }

    /**
     * Get the wildlife conflict incident this value belongs to.
     */
    public function wildlifeConflictIncident()
    {
        return $this->belongsTo(WildlifeConflictIncident::class);
    }
} 