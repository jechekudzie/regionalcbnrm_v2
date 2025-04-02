<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Organisation\PayableItem;

class PayableItemController extends Controller
{
    //index
    public function index()
    {
        $payableItems = PayableItem::all();
        return view('administration.payable_items.index', compact('payableItems'));
    }
}
