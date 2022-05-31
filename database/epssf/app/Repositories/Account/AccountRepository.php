<?php
namespace Ceb\Repositories\Account;
use Ceb\Models\Account;

/**
 * Account repositotyr
 */
class AccountRepository implements AccountRepositoryInterface {

	function __construct(Account $account) {
		$this->account = $account;
	}

	public function getAll() {
		return $this->account->all();
	}

	public function lists() {
		return $this->account->lists('entitled','account_number', 'id');
	}

	public function dropDownList()
	{
		$accounts = $this->account
						 ->orderBy('account_number')
						 ->get();

		foreach ($accounts as $account) {
			$accountsList[$account->id] = $account->account_number.' - '.$account->entitled.'(Acc #:'.$account->bank_account_number.')';
		}

		// Check if there any account we should not
		// include for the current  request segment
		$prohiddenAccounts = [];
		$prohiddens        = config('ceb.prohibiten_accounts.'.request()->segment(1));

		if (!empty($prohiddens)) {
			$prohiddenAccounts = $prohiddens;
		}
		
		foreach ($prohiddenAccounts as $account) {
			// Check if this account exists 
			// then remove it 
			$index = array_search($account, $accountsList);
			if ($index >= 0 ) {
				unset($accountsList[$index]);
			}
		}
		return $accountsList;
	}
}