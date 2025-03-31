<?php

namespace App\Models\HistoricalData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConflictRecord extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function organisation()
    {
        return $this->belongsTo(\App\Models\Admin\Organisation::class);
    }

    public function species()
    {
        return $this->belongsTo(\App\Models\Organisation\Species::class);
    }
}
