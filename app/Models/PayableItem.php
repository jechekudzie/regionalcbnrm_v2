<?php

namespace App\Models;

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
        return $this->belongsTo(Category::class);
    }

    public function organisationPayableItems()
    {
        return $this->hasMany(OrganisationPayableItem::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function revenueSharing()
    {
        return $this->hasMany(RevenueSharing::class);
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
