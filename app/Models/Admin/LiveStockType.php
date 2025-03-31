<?php

namespace App\Models\Admin;

use App\Models\HistoricalData\LiveStockConflictRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveStockType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the livestock conflict records associated with this livestock type.
     */
    public function liveStockConflictRecords()
    {
        return $this->hasMany(LiveStockConflictRecord::class);
    }
}
