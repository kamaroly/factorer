<?php

namespace Ceb\Http\Controllers;

use Illuminate\Http\Request;

use Ceb\Http\Requests;
use Ceb\Http\Controllers\Controller;

class AccountReconciliationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * Show reconciliations for bank forms
         */
        return view('reconciliations.banks.index');
    }
}
