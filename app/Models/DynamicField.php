<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class DynamicField extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

    //ConflictOutCome
    public function ConflictOutCome()
    {
        return $this->belongsTo(ConflictOutCome::class);
    }

    //dynamicFieldOptions
    public function options()
    {
        return $this->hasMany(DynamicFieldOption::class);
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
