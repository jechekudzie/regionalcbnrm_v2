<?php

namespace App\Models\Admin;

use App\Models\DynamicField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ConflictOutCome extends Model
{
    use HasFactory, HasSlug;

    protected $guarded = [];
    protected $organisationId = null;

    // Set the organisation ID to filter dynamic fields
    public function setOrganisationId($id)
    {
        $this->organisationId = $id;
        return $this;
    }

    // Get the currently set organisation ID
    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    // Incidents
    public function incidents()
    {
        // Custom pivot table and column names
        return $this->belongsToMany(WildlifeConflictIncident::class, 'incident_conflict_outcome', 'conflict_outcome_id', 'incident_id');
    }

    // ConflictType
    public function conflictType()
    {
        return $this->belongsTo(ConflictType::class);
    }

    // Dynamic fields - will filter by organisation if organisationId is set
    public function dynamicFields()
    {
        $relation = $this->hasMany(DynamicField::class, 'conflict_outcome_id');

        if ($this->organisationId) {
            $relation->where('organisation_id', $this->organisationId);
        }

        return $relation;
    }

    // Get dynamic field values for a specific incident
    public function getDynamicFieldValuesForIncident($incidentId)
    {
        $query = DB::table('conflict_outcome_dynamic_field_values as pivot')
            ->join('dynamic_fields as fields', 'pivot.dynamic_field_id', '=', 'fields.id')
            ->where('pivot.conflict_outcome_id', $this->id)
            ->where('pivot.incident_id', $incidentId)
            ->select('fields.label as fieldName', 'pivot.value as fieldValue', 'fields.id as fieldId');

        if ($this->organisationId) {
            $query->where('fields.organisation_id', $this->organisationId);
        }

        return $query->get();
    }

    // Scope to get conflict outcomes with dynamic fields
    public function scopeWithDynamicFields($query, $organisationId = null)
    {
        return $query->with(['dynamicFields' => function ($q) use ($organisationId) {
            if ($organisationId) {
                $q->where('organisation_id', $organisationId);
            }
        }]);
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
