<?php

namespace Modules\Reports\Http\Controllers\Backend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReportFilterController extends Controller
{

    /**
     * Show the filter for the reports
     *
     * @return mixed
     */
    public function index()
    {
        return view('reports::filters.index');
    }
}
