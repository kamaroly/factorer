<?php 
namespace Ceb\Factories;

use Ceb\Models\Account;
use Illuminate\Support\Collection;

class BankReconciliationFactory{

	/**
	 * Reconcile bank
	 * @return
	 */
	public function reconcile($accounts,$amounts)
	{
		$accounts = (new Collection($accounts))->flatten();
		$amounts  = (new Collection($amounts))->flatten();

		// 1. Get accounts to reconcile from database
		$accountsBalance = $this->accountsBalance($accounts);

		// 2. Build user submitted accounts / amount collection
		$bankData 		= $this->mergeBankData($accounts,$amounts);

		// 3. Compare accounts with what the user filled
		$accountBenchMark = $this->matchAccounts($accountsBalance,$bankData);
		
		// Save results in the sessions
		$this->setBankReconciliationResults($accountBenchMark);

		// Return results in the array
		return $accountBenchMark;
	}

	/**
	 * Get account balance in database
	 * @param   $accounts 
	 * @return  collection
	 */
	public function accountsBalance(Collection $accounts)
	{
		$accounts = Account::whereIn('id',$accounts);
		// If we have date consider them, it's probably account reconciliation
		if(!empty(request()->get('start_at')) and !empty(request()->get('end_at'))){
			$start = date('Y-m-d H:i:s',strtotime(request()->get('start_at')));
			$end = date('Y-m-d H:i:s',strtotime(request()->get('end_at')));

			$accounts = $accounts->whereHas('postings',function($query) use($start,$end){
				$query->whereBetween('updated_at',[$start,$end]);
			});
		}	

		$accounts = $accounts->get();

		return $accounts->transform(function($account){
				return [
					'id'            => $account->id,
					'name'          => $account->account_number.'-'.$account->entitled.'(Ac #:'.$account->bank_account_number.')',
					'debit_amount'  => $account->debit_amount,
					'credit_amount' => $account->credit_amount,
					'balance'       => $account->balance,
				];
		});
	}

	/**
	 * Merge data and map accounts and bank daa
	 * @param   $accounts 	
	 * @param   $amounts  	
	 * @return 	collection
	 */
	public function mergeBankData(Collection $accounts, Collection $amounts)
	{

		return $accounts->transform(function($account,$index) use ($amounts){
					return [
						'account_id' => $account,
						'bank_amount' => $amounts[$index]
					];
		});

	}

	/**
	 * Benchmark system and bank
	 * @param   $systemData 
	 * @param   $bankData   
	 * @return            
	 */
	public function matchAccounts(Collection $systemData,Collection $bankData)
	{

		return  $systemData->transform(function($account) use($bankData){
				$bank                    = $bankData->where('account_id',(string) $account['id'])->first();
				$account['bank_balance'] = $bank['bank_amount'];
				$account['status']       = ($account['bank_balance'] == $account['balance']) ? 'MATCH' : 'MISMATCH';

				return  $account;
		});
	}

	/**
	 * Set reconciliation results in the database
	 * @param  $accounts
	 */
	public function setBankReconciliationResults(Collection $accounts)
	{
		return session()->put('bank-reconciliations',$accounts);
	}

	/**
	 * Get bank reconciliation results
	 * @return  
	 */
	public function getReconciliations()
	{
		return session()->get('bank-reconciliations');
	}
}