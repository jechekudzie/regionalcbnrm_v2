<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ConflictOutCome extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

    //incidents
    public function incidents()
    {
        // Custom pivot table and column names
        return $this->belongsToMany(Incident::class, 'incident_conflict_outcome', 'conflict_outcome_id', 'incident_id');
    }

    //conflictType
    public function conflictType()
    {
        return $this->belongsTo(ConflictType::class);
    }

   //dynamic fields
    public function dynamicFields()
    {
        return $this->hasMany(DynamicField::class, 'conflict_outcome_id');
    }

    // In ConflictOutCome model

    public function getDynamicFieldValuesForIncident($incidentId)
    {
        return DB::table('conflict_outcome_dynamic_field_values as pivot')
            ->join('dynamic_fields as fields', 'pivot.dynamic_field_id', '=', 'fields.id')
            ->where('pivot.conflict_outcome_id', $this->id)
            ->where('pivot.incident_id', $incidentId)
            ->select('fields.label as fieldName', 'pivot.value as fieldValue', 'fields.id as fieldId')
            ->get();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }


    public function getRouteKeyName()
    {
        return 'slug';
    }
}
