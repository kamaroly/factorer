<?php

namespace Modules\Accounting\Models;

use Illuminate\Support\Facades\DB;

class Posting extends Model {

	protected $fillable = [
		'transactionid',
		'account_id',
		'journal_id',
		'asset_type',
		'amount',
		'user_id',
		'account_period',
		'transaction_type',
		'wording',
		'cheque_number',
		'bank',
	];


	/**
	 * Relationship with journal
	 * @return Ceb\Models\Journal
	 */
	public function journal()
	{
		return $this->belongsTo(Journal::class);
	}

	/**
	 * Relationship with Contribution
	 * @return Ceb\Models\Contribution
	 */
	public function contribution()
	{
		return $this->belongsTo(Contribution::class,'transactionid','transactionid');
	}

	/**
	 * Relationship with user
	 * @return Ceb\Models\user
	 */
	public function member()
	{
		return $this->belongsTo(User::class,'adhersion_id','member_adhersion_id');
	}

	/**
	 * Relationship with user
	 * @return Ceb\Models\user
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Relationship with Loan
	 * @return Ceb\Models\Loan
	 *
	 */
	public function loans()
	{
		return $this->belongsTo(Loan::class,'transactionid','transactionid');
	}

	/**
	 * Get postings before this date
	 * @param   $query
	 * @param   $date
	 * @return
	 */
	public function scopeBefore($query,$date)
	{
		return $query->where(DB::raw('created_at'),'<',$date);
	}

	/**
	 * Get by adhersion NUMBER
	 * @return
	 */
	public function getAdhersionIdAttribute()
	{
		// If we have contribution then use it's contribution Id
		if (! empty($contribution = $this->contribution)) {
			return $contribution->adhersion_id;
		}

		// Show Loan adherison Id if it's avalable
		if (! empty($loan = $this->loans)) {
			return $loan->adhersion_id;
		}

		// WE could not detect adhersion id for this loan
		return NULL;
	}

	/**
	 * Relationship with account
	 * @return Ceb\Models\Account
	 */
	public function account()
	{
		return $this->belongsTo(Account::class);
	}

	/**
	 * Get account Number attribute
	 * @return
	 */
	public function getAccountNumberAttribute()
	{
		return $this->account->account_number;
	}
	/**
	 * Debit transactions
	 * @return Ceb\Models\Posting
	 */
	public function scopeDebits($query)
	{
		return $query->where(DB::raw('LOWER(transaction_type)'),'debit');
	}

	/**
	 * Get posting by transaction ID
	 * @param  $query
	 * @param  $transactionId
	 * @return mixed
	 */
	public function scopeByTransaction($query,$transactionid)
	{
		return $query->where('transactionid',$transactionid);
	}

	/**
	 * Get posting by account
	 * @param  $query
	 * @param  $account_id
	 * @return
	 */
	public function scopeForAccount($query,$account_id)
	{
		return $query->where('account_id',$account_id);
	}
	/**
	 * Sum amount
	 *
	 * @return numeric
	 */
	public function scopeSumAmount($query)
	{
		return ($this->exists == true) ? $query->sum('amount') : 0 ;
	}
    /**
	 * Debit transactions
	 * @return Ceb\Models\Posting
	 */
	public function scopeCredits($query)
	{
		return $query->where(DB::raw('LOWER(transaction_type)'),'credit');
	}

	/**
	 * Get debit amount for this posting
	 *
	 * @return numeric
	 */
	public function getDebitAmountAttribute()
	{
		return strtolower($this->transaction_type) =='debit' ? $this->amount : 0;
	}

	/**
	 * Get credit account for this posting
	 * @return numeric
	 */
	public function getCreditAmountAttribute()
	{
		return strtolower($this->transaction_type) =='credit' ? $this->amount : 0;
	}

	/**
     * Get the posting's transaction type.
     *
     * @param  string  $value
     * @return string
     */
    public function getTransactionTypeAttribute($value)
    {
        return strtolower($value);
    }
}
