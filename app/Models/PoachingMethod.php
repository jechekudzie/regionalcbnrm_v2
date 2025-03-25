<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class PoachingMethod extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

    public function poachingIncidents()
    {
        return $this->belongsToMany(PoachingIncident::class, 'incident_poaching_methods');
    }

    public function poachingRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\PoachingRecord::class);
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
