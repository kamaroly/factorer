<?php

namespace Ceb\Http\Controllers;

use Ceb\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Ceb\Models\Account;
use Ceb\Http\Requests;
use Carbon\Carbon;

class AccountingReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trialBalance($endDate,$excel = 0)
    {
        // 1. Balance of actif accounts goes into Debit
        // 2. Balance of passif accounts goes into credit
        // 3. Balance of Produit accounts goes into debit
        // 4. Balance of charge goes to credit
        // 5. Make sum and get trial balance 

        //startdate  have been  hard  coded  because  ceb postings  start in 2017 no need  to choose  between  two date 
        $startDate='2017-01-01';
       
        if(ends_with($endDate,"12-31")) {
         $accounts = Account::with(['postingstrialBalance'=> function($query) use($startDate,$endDate) {
                                    $query->whereBetween(DB::raw('DATE(created_at)'),[$startDate,$endDate]);
                                 }])->where('account_number','<>','8700')->orderBy('account_number','ASC')->get();
  
        $report = view('reports.accounting.trial-balance',compact('accounts'));
        }
        else    {    
            $accounts = Account::with(['postingstrialBalance'=> function($query) use($startDate,$endDate) {
                                        $query->whereBetween(DB::raw('DATE(created_at)'),[$startDate,$endDate]);
                                     }])->orderBy('account_number','ASC')->get();
            $report = view('reports.accounting.trial-balance',compact('accounts'));    
        }

        return $this->render($report,'trial-balance'.$endDate,$excel);
    }

    /**
     * Generate Cash Flow stement Report
     * @param  integer $excel 
     * @return view
     */
    public function cashFlowStatement($startDate,$endDate,$excel = 0)

     {

        //dd($endDate);
        $accounts      =  Account::cashFlowStatement($startDate,$endDate)->sortBy('order');
        $balanceActif  = $accounts->where('account_nature','ACTIF')->sum('balance'); 
        $balancePassif = $accounts->where('account_nature','PASSIF')->sum('balance');

        //dd(abs($balanceActif) - abs($balancePassif));
        // Avoid negative display by considering absolute numbers
        $netIncome = abs(abs($balanceActif) - abs($balancePassif));
        
        // Generate report
        $report =  view('reports.accounting.cash-flow-statement',compact('accounts','startDate','endDate','netIncome'))->render();

        // Present report on the page.
        $daterange = Carbon::now();
        return $this->render($report,'cash-flow-statement'.$daterange,$excel);
    }
    /**
     * Display report
     * @param    $report 
     * @param    $title  
     * @param   $excel  
     * @return  string
     */
    private function render($report,$title = 'Report',$excel=0)
    {
        // Export in excel if required
        if ($excel==1) {
          return toExcel($report,'account-piece-report');
        }

        // Export PDF if required
        if (request()->has('pdf')) {
            return  htmlToPdf($report);
        }
        return view('layouts.printing', compact('report'));
    }

}
