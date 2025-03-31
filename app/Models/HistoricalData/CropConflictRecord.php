<?php

namespace App\Models\HistoricalData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CropConflictRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organisation_id',
        'species_id',
        'crop_type_id',
        'period',
        'hectrage_destroyed',
    ];

    /**
     * Get the organisation that owns the crop conflict record.
     */
    public function organisation()
    {
        return $this->belongsTo(\App\Models\Admin\Organisation::class);
    }

    /**
     * Get the species associated with the crop conflict record.
     */
    public function species()
    {
        return $this->belongsTo(\App\Models\Organisation\Species::class);
    }

    /**
     * Get the crop type associated with the crop conflict record.
     */
    public function cropType()
    {
        return $this->belongsTo(\App\Models\Admin\CropType::class);
    }
}
