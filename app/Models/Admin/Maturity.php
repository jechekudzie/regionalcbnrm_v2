<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Maturity extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

    public function populationEstimates()
    {
        return $this->hasMany(PopulationEstimate::class);
    }

    public function species()
    {
        return $this->hasMany(Species::class, 'maturity_id');
    }
    public function getSlugOptions() : SlugOptions
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
