<?php

namespace Ceb\Http\Controllers;

use Illuminate\Http\Request;

use Ceb\Models\Refund;
use Ceb\Http\Requests;
use League\Csv\Reader;
use Ceb\Http\Controllers\Controller;
use Ceb\Factories\RefundReconciliationFactory;


class RefundReconciliationController extends Controller
{
    protected $factory;

    function __construct(RefundReconciliationFactory $factory)
    {
        parent::__construct();
        $this->factory = $factory;
    }
    /**
     * Display a finding form
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Remove any item from previous reconciliation
        $this->factory->clear();

        return view('reconciliations.refunds.index');
    }

    /**
     * Show the list of found transactions
     *
     * @return \Illuminate\Http\Response
     */
    public function find()
    {
        // 1. Get all refund for this transactions
        $refunds = Refund::with('member')->byTransaction(request()->get('transactionid'))->get();

        if ($refunds->isEmpty()) {
            flash()->warning(trans('general.there_no_record_for_provided_transaction'));
            return $this->index();
        }
        // 2.Get important data to use for update
        $refundToReconcile = $refunds->transform(function($refund){
                $member = $refund->member->first();

                return [
                    'id'            => $refund->id,
                    'amount'        => (int) $refund->amount,
                    'adhersion_id'  => $refund->adhersion_id,
                    'nid'           => $member->member_nid,
                    'institution'   => $member->institution->name,
                    'employee_id'   => $member->employee_id,
                    'names'         => $member->first_name.' '.$member->last_name,
                    'transactionid' => $refund->transaction_id,
                    'amount_in_csv' => 0,
                ];
            });

        $this->factory->setDbRefunds($refundToReconcile);

        flash()->success(trans('Total record of '.$refundToReconcile->count(). ' found.' ));
        return $this->showCsvForm();
    }

    /**
     * Show form to import CSV
     * @return  response
     */
    public function  showCsvForm ()
    {
        return view('reconciliations.refunds.upload-csv');
    }

    /**
     * Import file in the session
     * @return  
     */
    public function upload()
    {
         if(request()->file('refunds-csv')->getClientOriginalExtension() != 'csv') {
            flash()->error('You must upload a csv file');
            return $this->showCsvForm();
         }

        // checking file is valid.
        if (request()->file('refunds-csv')->isValid()) {
           $csv     = Reader::createFromPath(request()->file('refunds-csv'));
           $csv->setOffset(1); //because we don't want to insert the header
           $refunds = collect($csv->fetchAll());
           // Set collect key names
           $refunds = $refunds->transform(function($item){
                return [
                    'adhersion_id' => $item[0],
                    'amount'       => (int) $item[1]
                ];
           });

           $this->factory->setCsvRefunds($refunds);
           $this->factory->reconcile();

           return $this->reload();
        }

        flash()->error(trans('Invalid CSV uploaded, kindly review and try again'));
        return $this->showCsvForm();
    }

    /**
     * Reload the page
     * @return  response
     */
    private function reload()
    {
        // Get reconciled refunds
        $notMatchingRefund = $this->factory->getNotMatchingRefund();
        $inBoth            = $this->factory->getInBoth();
        $onlyInDb          = $this->factory->getOnlyInDb();
        $onlyInCsv         = $this->factory->getOnlyInCsv();
        $dbRefunds         = $this->factory->dbRefunds();
        $csvRefunds        = $this->factory->csvRefunds();

        return view('reconciliations.refunds.reload',compact(
            'notMatchingRefund',
            'inBoth',
            'onlyInDb',
            'onlyInCsv',
            'dbRefunds',
            'csvRefunds'
        ));
    }

    /**
     * Download collections in csv
     * @param  string $type 
     * @return csv
     */
    public function download(string $type)
    {
     switch ($type) {
            case 'matching':
                $refundsToDownload = $this->factory->getInBoth();
                break;
            case 'not-matching':
                $refundsToDownload = $this->factory->getNotMatchingRefund();
                break;            
            case 'in-system-not-csv':
                $refundsToDownload = $this->factory->getOnlyInDb();
                break; 
            case 'in-csv-not-system':
                $refundsToDownload = $this->factory->getOnlyInCsv();
                break;
        }
        return collectionToCsv($refundsToDownload,$type);
    }
}
