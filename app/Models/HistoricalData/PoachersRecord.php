<?php

namespace App\Models\HistoricalData;

use App\Models\Organisation;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoachersRecord extends Model
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
        'arrested',
        'bailed',
        'sentenced',
        'period',
        'notes',
    ];

    /**
     * Get the organisation that owns the poachers record.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the species associated with the poachers record.
     */
    public function species()
    {
        return $this->belongsTo(Species::class);
    }
}
