<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationPayableItem extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function organisation()
    {
        return $this->belongsTo(\App\Models\Admin\Organisation::class);
    }

    public function payableItem()
    {
        return $this->belongsTo(\App\Models\Organisation\PayableItem::class);
    }

    //has many transaction payable items
    public function transactionPayableItems()
    {
        return $this->hasMany(\App\Models\Organisation\TransactionPayable::class);
    }
    public function species()
    {
        return $this->belongsToMany(\App\Models\Organisation\Species::class, 'organisation_payable_item_species', 'organisation_payable_item_id', 'species_id')
            ->withPivot('price');
    }


}
