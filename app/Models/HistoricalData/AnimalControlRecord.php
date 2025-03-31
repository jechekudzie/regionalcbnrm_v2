<?php

namespace App\Models\HistoricalData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalControlRecord extends Model
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
        'period',
        'number_of_cases',
        'killed',
        'relocated',
        'scared',
        'injured',
    ];

    /**
     * Get the organisation that owns the animal control record.
     */
    public function organisation()
    {
        return $this->belongsTo(\App\Models\Admin\Organisation::class);
    }

    /**
     * Get the species associated with the animal control record.
     */
    public function species()
    {
        return $this->belongsTo(\App\Models\Organisation\Species::class);
    }
}
