<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayable extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function transaction()
    {
        return $this->belongsTo(\App\Models\Organisation\Transaction::class);
    }

    public function payableItem()
    {
        return $this->belongsTo(\App\Models\Organisation\PayableItem::class);
    }

    //organisation payable item
    public function organisationPayableItem()
    {
        return $this->belongsTo(\App\Models\Organisation\OrganisationPayableItem::class);
    }
    public function species()
    {
        return $this->belongsToMany(\App\Models\Organisation\Species::class, 'transaction_payable_species', 'transaction_payable_id', 'species_id')
            ->withPivot('price');
    }

}
