<?php

namespace Ceb\Http\Controllers;

use Ceb\Factories\ContributionFactory;
use Ceb\Http\Controllers\Controller;
use Ceb\Models\Account;
use Ceb\Models\Institution;
use Ceb\Repositories\Contribution\ContributionRepository as Contribution;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\Response;
use Input;
use League\Csv\Reader;
use Log;

class ContributionAndSavingsController extends Controller {

	private $contribution;
	function __construct(Contribution $contribution, Institution $institution, Account $account, ContributionFactory $contributionFactory) {
		parent::__construct();
		$this->contribution        = $contribution;
		$this->account             = $account;
		$this->institution         = $institution;
		$this->contributionFactory = $contributionFactory;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
      // First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.index')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' viewed index of contribution');
    
		return $this->reload();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {

		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.add')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' started to add contribution');
    
		return view('contributionsandsavings.create');
	}

	/**
	 * Show contributions
	 * @return function
	 */
	public function show() {

	   // First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.view')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' started to view contribution');
    
		return $this->reload();
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id) {

	   // First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.update')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' updated contribution');
    
		// Update
		$adhersion_number = Input::get('adhersion_number');
		$monthly_fee = Input::get('monthly_fee');

		$this->contributionFactory->updateMonthlyFee($adhersion_number, $monthly_fee);

		return $this->reload();
	}

	/**
	 * Completing contribution transaction
	 * 
	 * @return mixed
	 */
	public function complete() {

	   // First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.complete')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' completed contribution');
    
		if (is_null(Input::get('wording')) || empty(Input::get('wording'))) {
			flash()->error(trans('contribution.you_must_write_wording_for_this_transaction'));
			return $this->reload();
		}

		$this->setByInsitution();
		$this->setDebitAccount();
		$this->setCreditAccount();
		$this->setMonth();
		$this->removeContributionWithDifference();

	    $this->contributionFactory->setWording(Input::get('wording'));

		$contributions    = $this->contributionFactory->getConstributions();
		$month            = $this->contributionFactory->getMonth();
		$debitAccount     = $this->contributionFactory->getDebitAccount();
		$creditAccount    = $this->contributionFactory->getCreditAccount();
		$contributionType = $this->contributionFactory->getContributionType();


		if (count($contributions) < 1 ) {
			flash()->error(trans('contribution.there_is_no_member_to_contribute_for'));
			return $this->reload();
		}

		if (empty($month) && !in_array($contributionType,['ANNUAL_INTEREST','BULK_WITHDRAW'])) {
			flash()->error(trans('contribution.please_select_month_you_are_contributing_to_before_completing_transaction'));
			return $this->reload();
		}

		if (empty($debitAccount)) {
			flash()->warning(trans('contribution.please_select_account_to_debit_before_completing_this_transaction'));
			return $this->reload();
		}

		if (empty($creditAccount)) {
			flash()->warning(trans('contribution.please_select_account_to_credit_before_completing_this_transaction'));
			return $this->reload();
		}

		if ($creditAccount == $debitAccount) {
		    flash()->warning(trans('contribution.debit_account_and_credit_account_cannot_be_the_same'));
			return $this->reload();		
		}
		$transactionid = null;

		if (($transactionid = $this->contribution->complete()) != false) {

			$this->contributionFactory->clearAll();
			$message = trans('contribution.contribution_well_saved');
			flash()->success($message);

			return $this->reload($transactionid);
		}
		$message = trans('contribution.something_went_wrong_while_saving_contribution');
		flash()->error($message);

		return redirect()->route('contributions.index');
	}

	/**
	 * Reload the current transactions pages
	 * @return mixed
	 */
	private function reload($transactionid=null) {

		// Detect if there is something to set or be removed
		$this->setByInsitution();
		$this->setDebitAccount();
		$this->setCreditAccount();
		$this->setMonth();
		$this->setContributionType();
		$this->setInterestYear();

		// This determines if we need to remove member with differences
		$this->removeContributionWithDifference();

		$month                     = $this->contributionFactory->getMonth();
		$debitAccount              = $this->contributionFactory->getDebitAccount();
		$creditAccount             = $this->contributionFactory->getCreditAccount();

		$contributionHasDifference = !$this->contributionFactory->getConstributionsWithDifference()->isEmpty();
		$uploadsWithErrors         = !$this->contributionFactory->getUploadWithErros()->isEmpty();
		$members                   = $this->getMembers();
		$pageLinks                 = $this->getMembersPageLinks();
		$institutionId             = $this->contributionFactory->getInstitution();
		$wording                   = $this->contributionFactory->getWording();
		$contributionType          = $this->contributionFactory->getContributionType();
		$interestYear              = $this->contributionFactory->getInterestYear();

		// Disable  export for different and errors 
		// if we are doing annual interests
		if ($contributionType === 'ANNUAL_INTEREST') {
			$contributionHasDifference = false;
			$uploadsWithErrors = false;
		}

		// If we are doing bulk withdraw then consider recurrence
		if (!empty($this->contributionFactory->getRecurrence())) {
			$wording = $wording. '| Bulk Withdraw for closing: '. $this->contributionFactory->getRecurrence();
		}

		$total                     = $this->contributionFactory->total();

		return view('contributionsandsavings.list',
					 compact(	    'contributionType',
					 				'interestYear',
									'members',
									'transactionid',
									'pageLinks',
									'wording',
									'institutionId',
									'total',
									'debitAccount',
									'creditAccount',
									'month',
									'contributionHasDifference',
									'uploadsWithErrors'
					 	 ));

	}

	/**
	 * Remove a member from the current contribution session
	 * 
	 * @param   $adhersion_id 
	 * @return  mixed
	 */
	public function removeMember($adhersion_id)
	{
	   // First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.remove.member')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' removed member contribution');
		$this->contributionFactory->removeMember($adhersion_id);
		return $this->reload();
	}

	/**
	 * Cancel contribution
	 */
	public function cancel() {
      // First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.cancel')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' cancel contribution');
		$this->contributionFactory->clearAll();
		$message = trans('contribution.contribution_cancelled');
		flash()->success($message);

		return redirect()->route('contributions.index');

	}

	/**
	 * Set multiple members by uploading a csv containing their adhersion id and amount
	 * 
	 * @return [type] [description]
	 */
	public function batch()
	{
		 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.batch.contribution')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' did batch contribution');

		if (!Input::hasFile('file')) {
			flash()->error('Please select a file to upload');
			return $this->reload();
		}
		 if(Input::file('file')->getClientOriginalExtension() != 'csv') {
		    //Flash::error('You must upload a csv file');
		    flash()->error(trans('You_must_upload_a_csv_file'));
		    return $this->index();
		  }

	    // checking file is valid.
	    if (Input::file('file')->isValid()) {
	      
			$csv     = Reader::createFromPath(Input::file('file'));
			$message = '';
			
			$csv->setOffset(1); //because we don't want to insert the header
			$members = $csv->fetchAll();
			
			// 1. If this is bulk withdraw then set them in a special way
			if ($this->contributionFactory->getContributionType() === 'BULK_WITHDRAW' ) {
				// Pluck members into one dimension array to facilitate upload
				$members = collect($members)->pluck('0');
				$this->contributionFactory->setWithdrawMembers($members);
				return $this->reload();
			}

            $this->contributionFactory->setMember($members);
		}

		return $this->reload();
	}

	/**
	 * Download sample batch
	 * @return csv
	 */
	public function downloadSample()
	{
		 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.download.sample')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' downloaded sample contribution csv');
		 //PDF file is stored under project/public/download/info.pdf
        $file= storage_path(). "/app/csv/batchcontribution-sample.csv";

        return Response::download($file, 'batchcontribution-sample.csv');
	}
	/**
	 * Get members who are set to contribute
	 * 
	 * @return Paginator
	 */
	private function getMembersPageLinks()
	{
		$currentPage = Input::get('page', 1);

		if (Input::has('show-member-with-difference') && Input::get('show-member-with-difference') == 'yes') {
			$members = $this->contributionFactory->getConstributionsWithDifference();
			return new Paginator($members,$members->count(),20,$currentPage);
		}	
		
		$members = $this->contributionFactory->getConstributions();
		return new Paginator($members,$members->count(),20,$currentPage);
	}

	/**
	 * Get members to be displayed
	 * @return collections
	 */
	private function getMembers()
	{
		$currentPage = Input::get('page', 1);
		if (Input::has('show-member-with-difference') && Input::get('show-member-with-difference') == 'yes') {
			return $this->contributionFactory->getConstributionsWithDifference()->forPage($currentPage,20);
		}	
		return $this->contributionFactory->getConstributions()->forPage($currentPage,20);
	}

	/**
	 * Set month for the contribution
	 * 
	 */
	private function setMonth()
	{
		 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.set.month')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' set contribution month');

		if (Input::has('month')) {
			$this->contributionFactory->setMonth(Input::get('month'));
		}
	}

	/**
	 * Set credit account for this contribution
	 */
	private function setCreditAccount()
	{
			 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.set.credit.account')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' set contribution credit account');

		if (Input::has('credit_account')) {
			$this->contributionFactory->setCreditAccount(Input::get('credit_account'));
		}
	}

	/**
	 * Set debit account for this contribution
	 */
	private function setDebitAccount()
	{
			 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.set.debit.account')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' set contribution debit account');

		if (Input::has('debit_account')) {
			$this->contributionFactory->setDebitAccount(Input::get('debit_account'));
		}
	}

	/**
	 * Set  Recurrence to the session
	 * 
	 */
	public function setRecurrence()
	{
		$this->contributionFactory->setRecurrence(request()->get('recurrence'));
	}

	/**
	 * Set members by institution
	 */
	private function setByInsitution()
	{
		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.set.by.institution')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' set contributions by institution');

		if (Input::has('institution') && !empty(Input::get('institution'))) {
			$this->contributionFactory->setInstitution(Input::get('institution'));
			$this->contributionFactory->setByInsitution(Input::get('institution'));
		}
	}

	/**
	 * Set contribution type
	 */
	public function setContributionType()
	{
		if (request()->has('contribution-type')) {
			$this->contributionFactory->setContributionType(request()->get('contribution-type'));
		}
	}

	/**
	 * Set contribution type
	 */
	public function setInterestYear()
	{
		if (request()->has('interest-year')) {
			$this->contributionFactory->setInterestYear(request()->get('interest-year'));
		}
	}
	/**
	 * Export to CSV
	 * @return [type] [description]
	 */
	public function export()
	{
		// this determines if we need to export member with differences
		$this->exportContributionWithDifference();

		// This determines if we have some numbers that has errors and help to remove them
		$this->exportContributionWithErrors();

	}

	/**
	 * Export contribution with differences
	 * 
	 * @return void
	 */
	public function exportContributionWithDifference()
	{
				// First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.export.contribution.with.differences')) {
            flash()->error(trans('Sentinel::users.noaccess').' - To export contribution with differences');
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' exports contribution with differences');

		if (Input::has('export-member-with-differences') && Input::get('export-member-with-differences') == 'yes') {
			$members = $this->contributionFactory->getConstributionsWithDifference();
			$report = view('contributionsandsavings.export_table',compact('members'))->render();
	
			toExcel($report, trans('contribution.with_difference'));
		}

	}

	/**
	 * Remove contribution with differences
	 * 
	 * @return void
	 */
	public function removeContributionWithDifference()
	{
				// First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.remove.contribution.with.differences')) {
            flash()->error(trans('Sentinel::users.noaccess').' - To remove contribution with differences');
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' removed contribution with differences');

		if (Input::has('remove-member-with-differences') && Input::get('remove-member-with-differences') == 'yes') {
			$this->contributionFactory->forgetWithDifferences();
		}
	}


	/**
	 * Export contribution with differences
	 * 
	 * @return void
	 */
	public function exportContributionWithErrors()
	{
				// First check if the user has the permission to do this
        if (!$this->user->hasAccess('contribution.export.contribution.with.errors')) {
            flash()->error(trans('Sentinel::users.noaccess').' - To export contribution with Errors');
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' exports contribution with Error');

		if (Input::get('export-member-with-errors') == 'yes') {
			$members = $this->contributionFactory->getUploadWithErros();
			$report = view('contributionsandsavings.export_upload_with_errors',compact('members'))->render();
			
			toExcel($report, trans('contribution.with_errors'));
		}

	}
}
