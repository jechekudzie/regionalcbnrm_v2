<?php

namespace App\Models;

use App\Models\HistoricalData\CropConflictRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CropType extends Model
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
     * Get the crop conflict records associated with this crop type.
     */
    public function cropConflictRecords()
    {
        return $this->hasMany(CropConflictRecord::class);
    }
}
