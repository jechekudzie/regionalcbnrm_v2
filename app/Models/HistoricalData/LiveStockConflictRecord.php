<?php

namespace App\Models\HistoricalData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveStockConflictRecord extends Model
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
        'live_stock_type_id',
        'killed',
        'injured',
        'period',
    ];

    /**
     * Get the organisation that owns the livestock conflict record.
     */
    public function organisation()
    {
        return $this->belongsTo(\App\Models\Admin\Organisation::class);
    }

    /**
     * Get the species associated with the livestock conflict record.
     */
    public function species()
    {
        return $this->belongsTo(\App\Models\Organisation\Species::class);
    }

    /**
     * Get the livestock type associated with the livestock conflict record.
     */
    public function liveStockType()
    {
        return $this->belongsTo(\App\Models\Admin\LiveStockType::class);
    }
}
