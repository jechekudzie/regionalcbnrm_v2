<?php

namespace App\Models\HistoricalData;

use App\Models\Organisation;
use App\Models\Species;

use Illuminate\Database\Eloquent\Model;

class HuntingRecord extends Model
{
    //
    protected $guarded = [];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }
}
