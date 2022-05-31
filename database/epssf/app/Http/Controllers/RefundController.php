<?php

namespace Ceb\Http\Controllers;

use Ceb\Factories\RefundFactory;
use Ceb\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Input;
use League\Csv\Reader;
use Redirect;
use flash;
class RefundController extends Controller {

	function __construct(RefundFactory $refundFactory) {
	
		parent::__construct();
		$this->refundFactory = $refundFactory;
		/** if we have any parameter passed, then set it  */
		$this->setAnyThing();

	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('refund.index')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is entering refund index');
	
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
        if (!$this->user->hasAccess('refund.update')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
        
        // First log 
        Log::info($this->user->email . ' is updating refund');
		// Update
		$adhersion_number = request()->get('adhersion_number');
		$monthly_fee = request()->get('monthly_fee');

		$this->refundFactory->updateRefundFee($adhersion_number, $monthly_fee);

		return $this->reload();
	}

	/**
	 * Complete refund transaction that is ongoing
	 * @return Redirect
	 */
	public function complete() {
 		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('refund.complete')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is completing refund');
		if (is_null(request()->get('wording')) || empty(request()->get('wording'))) {
			flash()->error(trans('refund.you_must_write_wording_for_this_transaction'));
			return $this->reload();
		}

		// $month = $this->refundFactory->getMonth();
		// if (is_null($month)) {
		// 	flash()->error(trans('refund.please_set_month_before_you_continue'));
		// 	return $this->reload();
		// }
	    $this->refundFactory->setWording(request()->get('wording'));

		// codes to complete transactions
		$transactionid = $this->refundFactory->complete();
		
		return $this->reload($transactionid);
	}

	public function show()
	{
		return $this->reload();
	}
	/**
	 * Cancel refund transaction that is ongoing
	 * @return Redirect
	 */
	public function cancel() {
		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('refund.cancel')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is cancelling refund');
		$this->refundFactory->cancel();
		flash()->success(trans('refund.successfully_cancelled'));

		return Redirect::route('refunds.index');
	}
	/**
	 * Reload the refund view
	 * @return
	 */
	private function reload($transactionid = null) {
		/** Remove differences if they exists */
		$this->removeRefundsWithDifference();

		$month       = $this->refundFactory->getMonth();
		$institution = $this->refundFactory->getInstitution();

		$debitAccount	= $this->refundFactory->getDebitAccount();
		$creditAccount	= $this->refundFactory->getCreditAccount();
		$members 		= $this->refundFactory->getRefundMembers();

		//Get current page form url e.g. &page=6
		$currentPage = request()->get('page', 1);

		//Create a new Laravel collection from the array data
  		$members = collect($members);

		// Get page links
		$pageLinks = new Paginator($members,$members->count(),20,$currentPage);

  		//Define how many items we want to be visible in each page
		$members = $members->forPage($currentPage,20);
		
		$totalRefunds				= $this->refundFactory->getTotalRefunds();
		$refundType					= $this->refundFactory->getRefundType();
		$refundLoanType				= $this->refundFactory->getRefundLoanType();

		$data = [
					'refundNature'				=> $this->refundFactory->getRefundNature(),
					'members'					=> $members,
					'pageLinks'					=> $pageLinks,
					'institution'				=> $institution,
					'transactionid'				=> $transactionid,
					'refundType'				=> $refundType,
					'refundLoanType'			=> $refundLoanType,
					'month'						=> $month,
					'totalRefunds'				=> $totalRefunds,
					'creditAccount'				=> $creditAccount,
					'debitAccount'				=> $debitAccount,
					'refundHasDifference'		=> ! $this->refundFactory->getRefundsWithDifference()->isEmpty(),
					'uploadHasErrors'			=> ! $this->refundFactory->getUploadWithErros()->isEmpty(),
					'membersWithoutEmergency'   => $this->refundFactory->getRefundsWithoutEmergencyLoans(),
				 ];

		return view('refunds.list', $data);
	}

	/**
	 * Set multiple members by uploading a csv containing their adhersion id and amount
	 * 
	 * @return [type] [description]
	 */
	public function batch()
	{
		 // First check if the user has the permission to do this
        if (!$this->user->hasAccess('refund.batch')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' did batch refund');

		if (!request()->hasFile('file')) {
			flash()->error('Please select a file to upload');
			return $this->reload();
		}

		 if(Input::file('file')->getClientOriginalExtension() != 'csv') {
		    flash()->error('You must upload a csv file');
		    return $this->index();
		  }

	    // checking file is valid.
	    if (Input::file('file')->isValid()) {
	       
	       // Set refund nature
	       // $this->refundFactory->setRefundNature(request()->get('refund_nature'));

	       $csv = Reader::createFromPath(Input::file('file'));

	       $message = '';

		   $csv->setOffset(1); //because we don't want to insert the header
	       $members = $csv->fetchAll();

           $this->refundFactory->setMember($members);
		}

		return $this->reload();
	}

	/**
	 * Export to CSV
	 * @return [type] [description]
	 */
	public function export()
	{
		// this determines if we need to export member with differences
		$this->exportRefundsWithDifference();

		// This determines if we have some numbers that has errors and help to remove them
		$this->exportRefundsWithErrors();

		// Export members without Emergency Loan
		$this->exportMembersWithoutEmergencyLoan();

	}

	/**
	 * Export refunds with differences
	 * 
	 * @return void
	 */
	public function exportRefundsWithDifference()
	{
		
		if (request()->has('export-member-with-differences') && request()->get('export-member-with-differences') == 'yes') {
				// First check if the user has the permission to do this
        if (!$this->user->hasAccess('refund.export.refunds.with.differences')) {
            flash()->error(trans('Sentinel::users.noaccess').' - To export refund with differences');
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' exports contribution with differences');

		$members = $this->refundFactory->getRefundsWithDifference();
		$report = view('refunds.export_table',compact('members'))->render();

		toExcel($report, trans('refund.with_difference'));
		}

	}
	

	/**
	 * Export refunds with differences
	 * 
	 * @return void
	 */
	public function exportRefundsWithErrors()
	{
		if (request()->get('export-member-with-errors') == 'yes') {
			// First check if the user has the permission to do this
	        if (!$this->user->hasAccess('refund.export.refund.with.errors')) {
	            flash()->error(trans('Sentinel::users.noaccess').' - To export refund with Errors');
	            return redirect()->back();
	        }

	        // First log
	        Log::info($this->user->email . ' exports refund with Error');			
			$members = $this->refundFactory->getUploadWithErros();
			$report = view('contributionsandsavings.export_upload_with_errors',compact('members'))->render();
			
			toExcel($report, trans('refund.with_errors'));
		}
	}

	/**
	 * Export Members with Loan
	 * @return  export
	 */
	public function exportMembersWithoutEmergencyLoan()
	{
		if (request()->get('export-member-without-emergency-loans') == 'yes') {
	        // First log
	        Log::info($this->user->email . ' exports refund without emergency loans');			
			$members = $this->refundFactory->getRefundsWithoutEmergencyLoans();
			$report = view('contributionsandsavings.export_members_without_emergency',compact('members'))->render();
			
			toExcel($report, trans('refund.without_emergency_loans'));
		}
	}

	/**
	 * Remove refunds with differences
	 * 
	 * @return void
	 */
	public function removeRefundsWithDifference()
	{
		if (request()->has('remove-member-with-differences') && request()->get('remove-member-with-differences') == 'yes'){
		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('refund.remove.refunds.with.differences')) {
            flash()->error(trans('Sentinel::users.noaccess').' - To remove refunds with differences');
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' removed refunds with differences');

			$this->refundFactory->forgetRefundsWithDifferences();
		}
	}
    

	/**
	 * Set anything that may have been passed
	 */
	private function setAnyThing() 
	{
		$this->setRefundType();
		$this->setInstitution();
		$this->setMonth();
		$this->setDebitAccount();
		$this->setCreditAccount();
		$this->setRefundLoanType();
	}


	/**
	 * Set institution
	 */
	private function setInstitution() {
		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('refund.set.institution')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }
        // First log 
        Log::info($this->user->email . ' is setting refund institution');
		// If the user has changed new institution
		if (request()->has('institution')) {
			$this->refundFactory->setInstitution(request()->get('institution'));
			$this->refundFactory->setByInsitution(request()->get('institution'));
		}
	}

	/**
	 * Set refund in the session
	 */
	public function setRefundType()
	{
		if (request()->has('refund_type')) {
			$this->refundFactory->setRefundType(request()->get('refund_type'));
		}
	}	

	public function setRefundLoanType()
	{
		if (request()->has('refund-loan-type')) {
			$this->refundFactory->setRefundLoanType(request()->get('refund-loan-type'));
		}
	}

	/**
	 * Set Debit account
	 */
	private function setDebitAccount() {
		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('refund.set.debit.account')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is setting debit account for refund');
		// If we have Debit account in the url , then set it
		if (request()->has('debit_account')) {
			$this->refundFactory->setDebitAccount(request()->get('debit_account'));
		}
	}
	/**
	 * Set Credit Account
	 */
	private function setCreditAccount() {
			// First check if the user has the permission to do this
        if (!$this->user->hasAccess('refund.set.credit.account')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is setting credit account for refund');
		// If we have Credit account in the url then set credit account
		if (request()->has('credit_account')) {
			$this->refundFactory->setCreditAccount(request()->get('credit_account'));
		}
	}
	/**
	 * Set Month
	 */
	private function setMonth() {
	    // First check if the user has the permission to do this
        if (!$this->user->hasAccess('refund.set.month')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is setting month for refund');
		// If we have month in the parameters set it
		if (request()->has('month')) {
			$this->refundFactory->setMonth(request()->get('month'));
		}
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
        if (!$this->user->hasAccess('refund.remove.member')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' removed member contribution');
    	
		$this->refundFactory->removeMember($adhersion_id);
		return $this->reload();
	}
}
