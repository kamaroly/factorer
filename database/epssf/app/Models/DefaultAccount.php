<?php

namespace Ceb\Models;

class DefaultAccount extends Model
{
    protected $table = 'default_accounts';
    
    protected $appends = ['account_real_id'];
    /**
     * Accounts related to this default account
     * @return Illumunate\Support\Collection
     */
    public function accounts()
    {
    	return $this->hasMany('\Ceb\Models\Account','account_number','account_id');
    }

    /**
     * Query account per Module
     * @param   $query      
     * @param   $moduleName 
     * @return  query
     */
    public function scopeOfModule($query,$moduleName)
    {
    	return $query->where('module',$moduleName);
    }

    /**
     * Get account Id attribute
     * @return 
     */
    public function getAccountRealIdAttribute()
    {
        return $this->accounts->first()->id;
    }

    /**
     * Query by function
     * @param   $query        
     * @param   $functionName 
     * @return  query
     */
    public function scopeOfFunction($query,$functionName)
    {
    	return $query->where('function',$functionName);
    }

    /**
     * Query type
     * @param   $query 
     * @param   $type  
     * @return  Query
     */
    public function scopeOfType($query,$type)
    {
    	return $query->where('type',$type);
    }

    /**
     * Debit accounts
     * @param   $query 
     * @return   query
     */
    public function scopeDebit($query)
    {
    	return $this->scopeOfType($query,'debit');
    }

    /**
     * Query
     * @param   $query 
     * @return  query
     */
    public function scopeCredit($query)
    {
    	return $this->scopeOfType($query,'credit');
    }

	 /**
     * Get the Default accounts for Batch Contribution Attribute.
     *
     * @param  string  $value
     * @return string
     */
	public function scopeBatchContribution($query)
	{
		return $this->scopeOfFunction($query,'batch_contribution');
	}

	 /**
     * Get the Default accounts for Ordinary Loan Attribute.
     *
     * @param  string  $value
     * @return string
     */
	public function scopeOrdinaryLoan($query)
	{
		return $this->scopeOfFunction($query,'ordinary_loan');
	}
	 /**
     * Get the Default accounts for Special Loan Attribute.
     *
     * @param  string  $value
     * @return string
     */
	public function scopeSpecialLoan($query)
	{
		return $this->scopeOfFunction($query,'special_loan');
	}
	 /**
     * Get the Default accounts for Social Loan Attribute.
     *
     * @param  string  $value
     * @return string
     */
	public function scopeSocialLoan($query)
	{
		return $this->scopeOfFunction($query,'social_loan');
	}


     /**
     * Get the Default accounts for Emergency Loan Attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function scopeEmergencyLoan($query)
    {
        return $this->scopeOfFunction($query,'emergency_loan');
    }
	 /**
     * Get the Default accounts for Regularisation Installments Attribute.
     *
     * @param  string  $value
     * @return string
     */
	public function scopeRegularisationInstallments($query)
	{
		return $this->scopeOfFunction($query,'regularisation_installments');
	}
	 /**
     * Get the Default accounts for Regularisation Amount Attribute.
     *
     * @param  string  $value
     * @return string
     */
	public function scopeRegularisationAmount($query)
	{
		return $this->scopeOfFunction($query,'regularisation_amount');
	}
	 /**
     * Get the Default accounts for Regularisation Mount Installments.
     *
     * @param  string  $value
     * @return string
     */
	public function scopeRegularisationMountInstallments($query)
	{
		return $this->scopeOfFunction($query,'regularisation_amount_installments');
	}
	 /**
     * Get the Default accounts for Member Ransaction Withdraw.
     *
     * @param  string  $value
     * @return string
     */
	public function scopeMemberTransactionWithdraw($query)
	{
		return $this->scopeOfFunction($query,'member_transaction_withdraw');
	}
	
	 /**
     * Get the Default accounts for Member transaction Saving.
     *
     * @param  string  $value
     * @return string
     */
	public function scopeMemberTransactionSaving($query)
	{
		return $this->scopeOfFunction($query,'member_transaction_saving');
	}
    /**
     * Get the Default accounts for Member transaction Saving.
     *
     * @param  string  $value
     * @return string
     */
    public function scopeMemberTransaction($query,$functionName)
    {
        return $this->scopeOfFunction($query,$functionName);
    }
	 /**
     * Get the Default accounts for Refunds Individual Attribute.
     *
     * @param  string  $value
     * @return string
     */
	public function scopeRefundsIndividual($query)
	{
		return $this->scopeOfFunction($query,'refunds_individual');
	}
	 /**
     * Get the Default accounts for Refunds Batch Attribute.
     *
     * @param  string  $value
     * @return string
     */
	public function scopeRefundsBatch($query)
	{
		return $this->scopeOfFunction($query,'refunds_batch');
	}

}
