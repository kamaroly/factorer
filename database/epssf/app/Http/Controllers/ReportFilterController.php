<?php

namespace Ceb\Http\Controllers;

use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests;
use Illuminate\Http\Request;

class ReportFilterController extends Controller
{
    public $filterOptions = [
        'member_search'          => false,
        'show_institution'       => false,
        'show_dates'             => false,
        'show_months'            => false,
        'show_exports'           => false,
        'show_loan_status'       => false,
        'show_accounts'          => false,
        'show_loan_types'        => false,
        'show_transaction_type'  => false,
        'show_transaction_input' => false,
        'show_member_status'     => false,
        'show_months_number'     => false,
    ];

    function __construct() {
        $this->filterOptions = (object) $this->filterOptions;
        parent::__construct();
    }

    /**
     * Show date filter 
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {  
         
         // If we have a show instition parameters, then make sure it is set here
        $parameters = $request->all();
        foreach ($parameters as $key => $value) {
            $this->filterOptions->$key = (boolean) $value;
        }


         $filterOptions = $this->filterOptions;
         $reportUrl = $request->get('reporturl');
         
         $title     = trans('report.'.str_replace('/','.',$reportUrl));
         
         return view('reports.filters.date_filter',compact('reportUrl','title','filterOptions'));
    }
}
