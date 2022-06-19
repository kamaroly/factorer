<?php
namespace Modules\Accounting\Repositories;

use DB;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\Journal;
use Modules\Accounting\Models\Posting;
use Modules\Accounting\Traits\TransactionTrait;

/**
 * Accounting repository class
 */
class AccountingRepository {

	use TransactionTrait;

	function __construct(Journal $journal, Account $account, Posting $posting) {
		$this->Journal = $journal;
		$this->account = $account;
		$this->posting = $posting;
	}

	/**
	 * Create new account transactions
	 * @param array $accouting contrains the data to use during accounting
	 */
	public function complete(array $accoutingData) {

		// Start by validating provided information
		if (!$this->isValidInput($accoutingData)) {
			return false;
		}
		// Start saving if something fails cancel everything
		DB::beginTransaction();

		// Since we are done let's make sure everything is cleaned fo
		// the next transaction
		$transactionid = $this->getTransactionId();
		$debits = $this->joinAccountWithAmount($accoutingData['debit_accounts'], $accoutingData['debit_amounts']);
		$credits = $this->joinAccountWithAmount($accoutingData['credit_accounts'], $accoutingData['credit_amounts']);

		// Since journal is not needed here instead of breaking code we are going to set it to 0
		$accoutingData['journal'] = 0;
		// now we have reached so we can continue with saving posting
		$savePostings = $this->savePostings($transactionid, $accoutingData['journal'], $debits, $credits,$accoutingData['wording'],$accoutingData['cheque_number'],$accoutingData['bank']);

		// Rollback the transaction via if one of the insert fails
		if ($savePostings == false) {
			DB::rollBack();
			return false;
		}
		// Lastly, Let's commit a transaction since we reached here
		DB::commit();
		return $transactionid;
	}

	/**
	 * Validate input information
	 * @param  array  $accoutingData
	 * @return bool true / false
	 */
	private function isValidInput(array $accoutingData) {

		// Check if all input has information
		$debits = $this->joinAccountWithAmount($accoutingData['debit_accounts'], $accoutingData['debit_amounts']);

		$credits = $this->joinAccountWithAmount($accoutingData['credit_accounts'], $accoutingData['credit_amounts']);

		return $this->isValidPosting($debits, $credits);
	}

}
?>
