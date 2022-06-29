<?php
namespace Modules\Accounting\Traits;

use DateTime;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Models\Posting;

trait TransactionTrait {
	/**
	 * Get unique transaction ID
	 * @return string
	 */
	public function getTransactionId() {
		// $transactionid = Posting::select(DB::raw('MAX( CAST( id AS UNSIGNED) ) as transactionid'))
		// 				->first()->transactionid;
        // Make sure we prefix this transaction with user
        $transactionid = intval(date('Ymd') . (string) auth()->user()->id.'01');
        // $transactionid = intval(date('Ymd').'01');
		do {
			$transactionid++;
        } // Already in the DB? Fail. Try again
        while (self::transactionExists($transactionid));

        return $transactionid;
	}
	   /**
     * Checks whether a key exists in the database or not
     *
     * @param $key
     * @return bool
     */
    private static function transactionExists($key)
    {
        $postingCount 		= Posting::where('transactionid', '=', $key)->limit(1)->count();

        return ($postingCount > 0 );
    }
	/**
	 * Search  adhresion key
	 * @param   $keyword
	 * @param   $data
	 * @return
	 */
	protected function searchAdhersionKey($keyword, $data) {
		foreach ($data as $key => $value) {
			$current_key = $key;

			if ($value['adhersion_id'] == $keyword) {
				return $current_key;
			}
		}
		return false;
	}

	/**
	 * Get unique contribution
	 * contract Number for the
	 * Transaction
	 */
	private function getContributionContractNumber() {
		// return  date('YmdHis') . (string) Sentry::getUser()->id;
		return  $this->getTransactionId();
	}

	/**
	 * Mapping the accounts with their amount
	 * @param array $accounts accounts IDs
	 * @param array $amounts  accounts Amount
	 */
	public function accountAmount(array $accounts, array $amounts) {

		$newData = [];
		foreach ($accounts as $key => $value) {

			    if (!isset($amounts[$key]['value']) && !empty($amounts[$key])) {
			    	$newData[$value] = $amounts[$key];
			    	continue;
			    }

				if ((empty($amounts[$key]['value']) || trim($amounts[$key]['value']) == '')) {
				// This account doesn't have amount therefore
				// Let's go to the next one
				continue;
			}
			$newData[$value['value']] = $amounts[$key]['value'];

		}
		// Make sure we remove anything with zero
		// or Empty value before we close
		return array_filter($newData);
	}

	/**
	 * Make an array with Accounds IDs and amount
	 * @param  array  $accounts containing accounts IDs
	 * @param  array  $amounts  containing account amount
	 * @return array that merges
	 */
	public function joinAccountWithAmount(array $accounts, array $amounts){

		$AccountWithAmmount = [];
		foreach ($accounts as $key => $value) {
			if (empty($amounts[$key]) || trim($amounts[$key]) == '' || $amounts[$key] == 0) {
				// This account doesn't have amount therefore
				// Let's go to the next one
				continue;
			}
			$AccountWithAmmount[$value] = $amounts[$key];
		}
		return $AccountWithAmmount;
	}

	/**
	 * Validate if the accounts and amount per accounts
	 * are valid before saving them in the database
	 * @return boolean
	 */
	public function isValidPosting($debits = null, $credits = null) {

		// Did we recieve arrays ?
		if (!is_array($debits) || !is_array($credits) || count($debits) == 0 || count($credits) == 0) {
			# We have nothing to do here, just return false
			flash()->error(trans('loan.debits_and_credits_transaction_doesnot_look_normal_please_check_inputs_and_try_again'));
			return false;
		}

		// First check if the sum of both credits
		// and Debits has same sum of amount so that
		// we can make sure that this double entry is balanced
		if (array_sum($credits) != array_sum($debits)) {
			// just exit because we have nothing to do here...
			flash()->error(trans('loan.debits_and_credits_amount_must_be_equal'));
			return false;
		}
		// Let's check if the user is trying to debit and credit
		// Same account, which is not allowed as per accounting laws
		if (count(array_intersect_key($debits, $credits)) > 0 ) {
			flash()->error(trans('loan.it_is_not_allowed_to_credit_and_debit_same_account_please_correct_and_try_again'));
			return false;
		}
		// Ahwii ! time to read the bible, let's exit here wi
		// Good news
		return true;
	}

	/**
	 * Save postings in the database
	 * @param  numeric  $accountId
	 * @param  numeric  $amount
	 * @param  numeric  $transactionId
	 * @param  numeric  $transactionumeric
	 * @param  integer  $journalId
	 * @return     bool
	 */
	public function savePosting($accountId, $amount, $transactionId, $transactionType, $journalId = 1,$wording=null,$cheque_number=null,$status='pending') {
		// First prepare data to use for the debit account
		// Once are have debited(deducted data) then we can
		// Credit the account to be credited
		$posting['transactionid'] 		= $transactionId;
		$posting['account_id'] 			= $accountId;
		$posting['journal_id'] 			= $journalId; // We assume per default we are using journal 1
		$posting['asset_type'] 			= null;
		$posting['amount'] 				= $amount;
		$posting['user_id'] 			= auth()->user()->id;
		$posting['account_period'] 		= date('Y');
		$posting['transaction_type'] 	= $transactionType;
		$posting['wording']				= $wording;
		$posting['cheque_number']		= $cheque_number;
		$posting['status']				= $status;

		// Try to post the debit before crediting another account
		return $this->posting->create($posting);

	}

	/**
	 * Save posting to the database
	 * @param  STRING $transactionId UNIQUE TRANSACTIONID
	 * @return bool
	 */
	private function savePostings($transactionId, $journalId = 1, $debits = null, $credits = null,$wording=null,$cheque_number=null) {

		// Start by validating the information we
		// are about to svae in our database
		if (!$this->isValidPosting($debits,$credits)) {
			return false;
		}

		//Debiting....
		$debits = (!is_null($debits)) ? $debits : $this->getDebitAccounts();


		foreach ($debits as $accountId => $amount) {

			$results = $this->savePosting($accountId,(int) $amount, $transactionId, 'Debit', $journalId,$wording,$cheque_number);
			if (!$results) {
				flash()->error(trans("posting.something_went_wrong_while_trying_to_debit_accounts_please_check_input_and_try_again"));
				return false;
			}
		}

		//Crediting
		$credits = (!is_null($credits)) ? $credits : $this->getCreditAccounts();
		foreach ($credits as $accountId => $amount) {
			$results = $this->savePosting($accountId,(int) $amount, $transactionId, 'Credit', $journalId,$wording,$cheque_number);
			if (!$results) {
			     flash()->error(trans("posting.something_went_wrong_while_trying_to_credit_accounts_please_check_input_and_try_again"));
				return false;
			}
		}
		// We are safe here
		return true;
	}

}
