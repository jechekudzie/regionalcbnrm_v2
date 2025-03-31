<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class PayableItem extends Model
{
    use HasFactory, HasSlug;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(\App\Models\Admin\Category::class);
    }

    public function organisationPayableItems()
    {
        return $this->hasMany(\App\Models\Organisation\OrganisationPayableItem::class);
    }
    public function transactions()
    {
        return $this->hasMany(\App\Models\Organisation\Transaction::class);
    }

    public function revenueSharing()
    {
        return $this->hasMany(\App\Models\Organisation\RevenueSharing::class);
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
