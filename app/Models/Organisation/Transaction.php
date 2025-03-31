<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function organisation()
    {
        return $this->belongsTo(\App\Models\Admin\Organisation::class);
    }

    public function transactionPayables()
    {
        return $this->hasMany(\App\Models\Organisation\TransactionPayable::class);
    }

    public function huntingActivities()
    {
        return $this->hasMany(\App\Models\Admin\HuntingActivity::class);
    }
}
