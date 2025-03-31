<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Gender extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

    public function poachers()
    {
        return $this->hasMany(Poacher::class);
    }

    public function humanConflictRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\HumanConflictRecord::class);
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
