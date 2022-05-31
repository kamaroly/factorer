<?php

namespace Ceb\Http\Controllers;

use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests\AccountingRequest;
use Ceb\Repositories\Accounting\AccountingRepository;
use Log;
class AccountingController extends Controller {

	function __construct(AccountingRepository $AccountingRepository) {
		$this->accounting = $AccountingRepository;
		parent::__construct();
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {

		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('accounting.view')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' starts to view accounting forms');
		return $this->reload();
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(AccountingRequest $request) {

		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('accounting.posting')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' completed account posting');
        $transactionid = null;

		if (($transactionid = $this->accounting->complete($request->all())) != false) {
			flash()->success(trans('accounting.you_have_done_accounting_transaction_successfully',['transactionid'=>$transactionid]));
		  return $this->reload($transactionid);
		}

		flash()->success(trans('accounting.error_occured_while_completing_accounting_transaction'));
		return $this->reload();
	}

	/**
	 * Reload accounting page
	 * @param   $transactionid 
	 * @return  
	 */
	private function reload($transactionid=null) {
		return view('accounting.index',compact('transactionid'));
	}
}
