<?php

namespace Modules\Reports\Http\Controllers\Backend;

use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Entities\Purchase;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;


class ProductInventoryController extends Controller
{

    /**
     * Show the filter for the reports
     *
     * @return mixed
     */
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

        return view('reports::product-inventory', compact('purchases'));
    }
}
