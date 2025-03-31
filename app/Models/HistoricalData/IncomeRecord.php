<?php

namespace App\Models\HistoricalData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeRecord extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function organisation()
    {
        return $this->belongsTo(\App\Models\Admin\Organisation::class);
    }
}
