<?php

namespace App\Models;

use App\Models\Admin\ConflictType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Sluggable\SlugOptions;

class ConflictOutcome extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'conflict_out_comes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the dynamic fields associated with this conflict outcome.
     */
    public function dynamicFields()
    {
        return $this->hasMany(DynamicField::class, 'conflict_outcome_id');
    }


    //conflictType
    public function conflictType()
    {
        return $this->belongsTo(ConflictType::class);
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
