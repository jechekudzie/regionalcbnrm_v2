<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function transactionPayables()
    {
        return $this->hasMany(TransactionPayable::class);
    }

    public function huntingActivities()
    {
        return $this->hasMany(HuntingActivity::class);
    }
}
