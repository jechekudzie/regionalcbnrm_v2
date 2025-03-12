<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Module extends Model
{
    use HasFactory, HasSlug;

    protected $guarded = [];

   /*  protected static function booted()
    {
        static::created(function ($module) {
            $actions = ['view', 'create', 'read', 'update', 'delete'];

            foreach ($actions as $action) {
                $permissionName = strtolower($action . '-' . $module->slug);
                Permission::create(['name' => $permissionName]);
            }
        });
    } */

    public function permissions()
    {
        return Permission::where('name', 'like', '%-' . $this->slug);
    }

    public function getPermissionCountAttribute()
    {
        return $this->permissions()->count();
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
