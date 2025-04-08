<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Species extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'scientific_name',
        'description',
        'category',
        'status'
    ];

    /**
     * Get the poachers records associated with this species.
     */
    public function poachersRecords()
    {
        return $this->hasMany(HistoricalData\PoachersRecord::class);
    }
} 