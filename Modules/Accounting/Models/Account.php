<?php

namespace Modules\Accounting\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Account extends Model {

	/**
	 * Additional eloquent fields
	 * @var array
	 */
	protected $appends = ['balance','credit_amount','debit_amount'];

	/**
	 * Get posting / accounting transactions made for this model
	 * @return Posting
	 */
	public function postings()
	{
		return $this->hasMany(Posting::class);
	}

	 /** Get posting / for  trial balance
	 * @return Posting  withouth  closing transaction
	 */
	public function postingstrialBalance()
	{
		return $this->hasMany(Posting::class);
	}


	/**
	 * Debit transactions
	 * @return Posting
	 */
	public function debits()
	{
		return $this->postings()->where(DB::raw('LOWER(transaction_type)'),'debit');
	}

	/**
	 * Get Debit without Cloture
	 * @return  query
	 */
	public function debitWithoutCloture()
	{
		return $this->postings()->where(DB::raw('LOWER(transaction_type)'),'debit')
                                ->where('status','NOT LIKE','closing%');

	}

	/**
	 * Get Debit without normal trial balance
	 * @return  query
	 */
	public function debitTrialBalance()
	{
		return $this->postings()->where(DB::raw('LOWER(transaction_type)'),'debit')
                                ->where('status','NOT LIKE','closing%');

	}

	/**
	 * Debit transactions
	 * @return Posting
	 */
	public function credits()
	{
		return $this->postings()->where(DB::raw('LOWER(transaction_type)'),'credit');
	}

	/**
	 * Get postings without cloture
	 * @return query
	 */
	public function creditWithoutCloture()
	{
		return $this->postings()->where(DB::raw('LOWER(transaction_type)'),'credit')
                                  ->where('status','NOT LIKE','closing%');
	}

	/**
	 * Get postings for normal Trial  Balance
	 * @return query
	 */
	public function creditTrialBalance()
	{
		return $this->postings()->where(DB::raw('LOWER(transaction_type)'),'credit')
                                  ->where('status','NOT LIKE','closing%');
	}

	/**
	 * Get this account postings between two dates
	 * @param   $start
	 * @param   $end
	 * @return  collection
	 */
	public function postingsBetween($start,$end)
	{
		return $this->postings()->whereBetween(DB::raw('date(created_at)'),[$start,$end])->get();
	}


	/**
	 * Get debit amount sum  for this account
	 * @return numeric
	 */
	public function getDebitAmountAttribute()
	{
		return $this->debits()->sum('amount');
	}

	/**
	 * Get credit amount sum  for this account
	 * @return numeric
	 */
	public function getCreditAmountAttribute()
	{
		return $this->credits()->sum('amount');
	}

	/**
	 * Account Balance
	 * @return number
	 */
	public function getBalanceAttribute()
	{
		return $this->credit_amount - $this->debit_amount;
	}

	/**
	 * Get Cash Flow Statement report
	 * @return
	 */
	public static function cashFlowStatement($startDate,$endDate)
	{
		$accounts = DB::select("SELECT
							 id,
							 account_nature,
							 CASE
								WHEN account_nature IN ('PASSIF','PRODUITS') THEN 'Outflow'
							    WHEN account_nature IN ('ACTIF','CHARGES') THEN 'Inflow'
							 END as flow,
							 account_number,
							 entitled,
							 (SELECT SUM(amount) FROM postings WHERE account_id = accounts.id AND transaction_type = 'debit' AND date(created_at) BETWEEN ? and ?) AS debit,
							 (SELECT SUM(amount) FROM postings WHERE account_id = accounts.id AND transaction_type = 'credit' AND date(created_at) BETWEEN ? and ?) AS credit
							FROM accounts;"
						,[$startDate,$endDate,$startDate,$endDate]);

		// Calculate balance before returning results
	  	return collect($accounts)->transform(function($account){
				$account->balance  = $account->credit - $account->debit;
				$account->activity = 'others';
				$account->order    = 4;

	  			 // Add Account Activities operating account
	  			 if (in_array($account->account_number,config('ceb.operating_activities_accounts'))) {
	  			 	 $account->activity = 'operating_activities';
	  			 	 $account->order    = 1;
	  			 }
	  			 // Specify investiment activities
	  			 if (in_array($account->account_number,config('ceb.investiment_activities_accounts'))) {
	  			 	 $account->activity = 'investiment_activities';
	  			 	 $account->order    = 2;
	  			 }
 	  			 // Specify finance activities account
	  			 if (in_array($account->account_number,config('ceb.financing_activities_accounts'))) {
	  			 	 $account->activity = 'financing_activities';
 	  			 	 $account->order    = 3;
	  			 }

	  			 return $account;
	  	});
	}
}
