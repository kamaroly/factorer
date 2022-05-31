<?php

namespace Ceb\Http\Controllers;

use Illuminate\Http\Request;

use Ceb\Http\Requests;
use League\Csv\Reader;
use Ceb\Models\Contribution;
use Ceb\Http\Controllers\Controller;
use Ceb\Factories\ContributionReconciliationFactory;

class ContributionReconciliationController extends Controller
{
    protected $factory;

    function __construct(ContributionReconciliationFactory $factory)
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

        return view('reconciliations.contributions.index');
    }

    /**
     * Show the list of found transactions
     *
     * @return \Illuminate\Http\Response
     */
    public function find()
    {
        // 1. Get all Contribution for this transactions
        $contributions = Contribution::with('member')->byTransaction(request()->get('transactionid'))->get();

        if ($contributions->isEmpty()) {
            flash()->warning(trans('general.there_no_record_for_provided_transaction'));
            return $this->index();
        }
        // 2.Get important data to use for update
        $contributionToReconcile = $contributions->transform(function($contribution){
                $member = $contribution->member->first();
                return [
                    'id'            => $contribution->id,
                    'amount'        => (int) $contribution->amount,
                    'adhersion_id'  => $contribution->adhersion_id,
                    'nid'           => $member->member_nid,
                    'institution'   => $member->institution->name,
                    'employee_id'   => $member->employee_id,
                    'names'         => $member->first_name.' '.$member->last_name,
                    'transactionid' => $contribution->transaction_id,
                    'amount_in_csv' => 0,
                ];
            });

        $this->factory->setDbContributions($contributionToReconcile);

        flash()->success(trans('Total record of '.$contributionToReconcile->count(). ' found.' ));
        return $this->showCsvForm();
    }

    /**
     * Show form to import CSV
     * @return  response
     */
    public function  showCsvForm ()
    {
        return view('reconciliations.contributions.upload-csv');
    }

    /**
     * Import file in the session
     * @return  
     */
    public function upload()
    {
         if(request()->file('contributions-csv')->getClientOriginalExtension() != 'csv') {
            flash()->error('You must upload a csv file');
            return $this->showCsvForm();
         }

        // checking file is valid.
        if (request()->file('contributions-csv')->isValid()) {
           $csv     = Reader::createFromPath(request()->file('contributions-csv'));
           $csv->setOffset(1); //because we don't want to insert the header
           $contributions = collect($csv->fetchAll());
           // Set collect key names
           $contributions = $contributions->transform(function($item){
                return [
                    'adhersion_id' => (int) $item[0], // Use int to make it detectable on restrict
                    'amount'       => (int) $item[1]
                ];
           });

           $this->factory->setCsvContributions($contributions);
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
        // Get reconciled Contributions
        $notMatchingContribution = $this->factory->getNotMatchingContribution();
        $inBoth                  = $this->factory->getInBoth();
        $onlyInDb                = $this->factory->getOnlyInDb();
        $onlyInCsv               = $this->factory->getOnlyInCsv();
        $dbContributions         = $this->factory->dbContributions();
        $csvContributions        = $this->factory->csvContributions();


        return view('reconciliations.contributions.reload',compact(
            'notMatchingContribution',
            'inBoth',
            'onlyInDb',
            'onlyInCsv',
            'dbContributions',
            'csvContributions'
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
                $contributionsToDownload = $this->factory->getInBoth();
                break;
            case 'not-matching':
                $contributionsToDownload = $this->factory->getNotMatchingContribution();
                break;            
            case 'in-system-not-csv':
                $contributionsToDownload = $this->factory->getOnlyInDb();
                break; 
            case 'in-csv-not-system':
                $contributionsToDownload = $this->factory->getOnlyInCsv();
                break;
        }
        return collectionToCsv($contributionsToDownload,$type);
    }
}
