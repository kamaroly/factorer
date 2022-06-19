<?php

namespace Modules\Accounting\Http\Controllers\Backend;

use Log;
use App\Http\Controllers\Controller;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Http\Requests\AccountingRequest;
use Modules\Accounting\Repositories\AccountingRepository;


class AccountingController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
    {

        // First log
        Log::info(auth()->user()->email . ' starts to view accounting forms');
		return $this->reload();
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(AccountingRequest $request) {

        // First log
        Log::info(auth()->user()->email . ' completed account posting');
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
		return view('accounting::backend.accounting.index',[
            'transactionid' => $transactionid,
            'accounts' => Account::pluck('entitled', 'id'),
            'accounts_for_js' => Account::all(),
        ]);
	}
}
