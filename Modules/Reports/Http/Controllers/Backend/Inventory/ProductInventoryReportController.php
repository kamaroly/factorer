<?php

namespace Modules\Reports\Http\Controllers\Backend\Inventory;

use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Entities\Purchase;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;


class ProductInventoryReportController extends Controller
{

    public function index()
    {

        request()->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        $purchases =  Purchase::dateBetween(
                        request()->start_date,
                        request()->end_date
                    )->get();

        $purchaseBefore = Purchase::before(request()->start_date)->get();

        return view('reports::inventory.product-inventory', compact('purchases', 'purchaseBefore'));
    }
}
