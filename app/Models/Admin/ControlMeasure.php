<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ControlMeasure extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

    //incidents
    public function incidents()
    {
        // Custom pivot table and column names
        return $this->belongsToMany(Incident::class, 'incident_control_measure', 'control_measure_id', 'incident_id');
    }

    public function pacDetails()
    {
        return $this->belongsToMany(PACDetail::class, 'pac_detail_control_measure', 'control_measure_id', 'pac_detail_id')
            ->withPivot(['male_count', 'female_count', 'location', 'latitude', 'longitude', 'remarks']);
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
