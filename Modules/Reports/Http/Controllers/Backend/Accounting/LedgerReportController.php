<?php

namespace Modules\Reports\Http\Controllers\Backend\Accounting;

use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Entities\Purchase;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;


class LedgerReportController extends Controller
{

    public function index()
    {

        request()->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        return view('reports::accounting.ledger');
    }
}
