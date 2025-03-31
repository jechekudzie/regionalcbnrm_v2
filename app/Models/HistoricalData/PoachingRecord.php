<?php

namespace App\Models\HistoricalData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoachingRecord extends Model
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
        'poaching_method_id',
        'number',
        'period',
        'location',
        'notes',
    ];

    /**
     * Get the organisation that owns the poaching record.
     */
    public function organisation()
    {
        return $this->belongsTo(\App\Models\Admin\Organisation::class);
    }

    /**
     * Get the species associated with the poaching record.
     */
    public function species()
    {
        return $this->belongsTo(\App\Models\Organisation\Species::class);
    }

    /**
     * Get the poaching method associated with the poaching record.
     */
    public function poachingMethod()
    {
        return $this->belongsTo(\App\Models\Admin\PoachingMethod::class);
    }
}
