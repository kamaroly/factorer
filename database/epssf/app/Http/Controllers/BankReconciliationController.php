<?php

namespace Ceb\Http\Controllers;

use Ceb\Factories\BankReconciliationFactory;
use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests;
use Ceb\Models\Account;
use Ceb\Models\Posting;
use Illuminate\Http\Request;

class BankReconciliationController extends Controller
{
    /**
     * Bank reconciliation factory
     * @var BankReconciliationFactory
     */
    protected $factory;

    /**
     * Get 
     * @param BankReconciliationFactory $factory [description]
     */
    public function __construct(BankReconciliationFactory $factory)
    {
        $this->factory = $factory;
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         return view('reconciliations.banks.index');
    }

    /**
     * Reconcile bank
     *
     * @return \Illuminate\Http\Response 
     */
    public function reconcileBank()
    {
        $uploadedData = $this->parseCsv('bank-csv-file')->transform(function($row){
                                $row['credit'] = empty($row['credit']) ? 0: intval(str_replace(",","",$row['credit']));
                                $row['debit']  = empty($row['debit']) ? 0: intval(str_replace(",","",$row['debit']));

                                // These data will help us on reconciliation pannel on
                                $row['amount']           = ($row['debit'] === 0) ? $row['credit'] : $row['debit']; 
                                $row['transaction_type'] = ($row['debit'] === 0) ? 'credit' : 'debit'; 
                                return $row;
                        });

        $transactions = Account::findorFail(request('account'))->postingsBetween(request('start_at'), request('end_at'));

        // Store uploaded data in the current user session
        session()->put('bank-transactions',$uploadedData);
        session()->put('ceb-transactions',$transactions);

        return view('reconciliations.banks.index',compact('transactions','uploadedData'));
    }

    /**
     * Export reconciliations
     * @return download
     */
    public function download()
    {
        $bankTransactions = session('bank-transactions');
        $cebTransactions  = session('ceb-transactions');

        // Determine what to export
        if (request('download_type') == trans('general.export_matching_bank')) {
            if (! request()->has('matching_bank')) {
               flash('No Bank transaction matching selected / found. Please select file and retry or use previous opened tab.')->warning();
                return $this->index();
            }

            $dataToExport = [];
            foreach ($bankTransactions as $key => $transaction) {
                if ( in_array($key,request('matching_bank'))) {
                     $dataToExport[] = $transaction;
                }
            }
            $dataToExport = collect($dataToExport);
        }

        if (request('download_type') == trans('general.export_matching_ceb')) {
            if (! request()->has('matching_ceb')) {
               flash('No CEB transaction matching selected / found. Please select file and retry or use previous opened tab.')->warning();
                return $this->index();
            }
            $dataToExport =  $cebTransactions->filter(function($cebTransaction){
                            return in_array($cebTransaction->id, request('matching_ceb'));
                        });
        }

        if (request('download_type') == trans('general.export_not_in_ceb')) {
            if (! request()->has('matching_bank')) {
                $dataToExport = $bankTransactions;
            }else{
                $data = [];
                foreach( $bankTransactions as $key => $transaction) {
                        if ( ! in_array($key,request('matching_bank'))) {
                         $data[] = $transaction;
                    }
                }
                $dataToExport = collect($data);
            }
        }

        if (request('download_type') == trans('general.export_not_in_bank')) {

            if (! request()->has('matching_ceb')) {
                $dataToExport = $bankTransactions;
            }else{
            $dataToExport =  $cebTransactions->filter(function($cebTransaction){
                            return ! in_array($cebTransaction->id, request('matching_ceb'));
                        });
            }
        }

         // Display warning message if session has expired
        if ( $dataToExport->isEmpty()) {
               flash('There is no Data to Export.')->warning();
                return $this->index();
        }
        
        return collectionToCsv($dataToExport,request('download_type').'-'.date('Y-m-d-h-i-s'));
    }
}
