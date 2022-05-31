<?php

namespace Ceb\Models;

use Illuminate\Support\Facades\DB;

class MemberLoanCautionneur extends Model {
	
	protected  $table = 'member_loan_cautionneurs';


	/**
	 * Get the member who were given this loan
	 * @return User Object
	 */
   public function member()
   {
	   	return $this->belongsTo('\Ceb\Models\User','member_adhersion_id','adhersion_id');
   }

   	/**
	 * Get the member who has this caution
	 * @return User Object
	 */
   public function cauttionneur()
   {   
	   	return $this->belongsTo('\Ceb\Models\User','cautionneur_adhresion_id','adhersion_id');
   }

	/**
	 * Get member loans
	 */
	public function loan() 
	{
		return $this->belongsTo('\Ceb\Models\Loan');
	}

	/**
	 * Get caution details by loan Id
	 * @param  $query  
	 * @param  $loanId 
	 * @return Illumunate\Support\Querybuilder
	 */
	public function scopeByLoanId($query,$loanId)
	{
		return $query->where('loan_id',$loanId);
	}

	/**
	 * Get caution details by adhersion Id
	 * @param  $query  
	 * @param  $adhersion_id 
	 * @return Illumunate\Support\Querybuilder
	 */
	public function scopeByAdhersion($query,$adhersionId)
	{
		return $query->where('member_adhersion_id',$adhersionId);
	}

	/**
	 * Get caution details by adhersion Id
	 * @param  $query  
	 * @param  $adhersion_id 
	 * @return Illumunate\Support\Querybuilder
	 */
	public function scopeByCautionneurAdhersion($query,$adhersionId)
	{   
		return $query->where('cautionneur_adhresion_id',$adhersionId);
	}

	/**
	 * Get caution details by adhersion Id
	 * @param  $query  
	 * @param  $adhersion_id 
	 * @return Illumunate\Support\Querybuilder
	 */
	public function scopeByTransaction($query,$transactionId)
	{
		return $query->where('transaction_id',$transactionId);
	}

	/**
	 * Get member with active caution  during choosing cautionneur 
	 * @return query builder
	 */
	public function scopeActive($query)
	{
		return $query->distinct()
					->select('member_adhersion_id')
					->where('amount','>',DB::raw('refunded_amount'));
	}
    
    /**
	 * Get member with active caution for dashboard and  reports
	 * @return query builder
	 */
	public function scopeActivedash($query)
	{
		return $query->where('amount','>',DB::raw('refunded_amount'));
	}

	/**
	 * Inactive cautionss
	 * @param   $query 
	 * @return  query builder
	 */
	public function scopeInactive($query)
	{
		return $query->where('amount','<=','refunded_amount');
	}

	/**
	 * Get balance attribute
	 * @return number
	 */
	public function getBalanceAttribute()
	{
		return ($this->amount - $this->refunded_amount);
	}
	
	/**
	 * Get status attribute
	 * @return number
	 */
	public function getStatusAttribute()
	{
		return ($this->balance > 0 ) ? trans('general.active') : trans('general.closed');
	}

	/**
	 * Get active members
	 * @param   $query 
	 * @return 	
	 */
	public function activeMembers()
	{
		return $this->active()->select(DB::raw('DISTINCT member_adhersion_id'))->get();
	}

	/**
	 * Get active loans
	 * @param   $query 
	 * @return 	
	 */
	public function activeLoans()
	{
		return $this->active()->select(DB::raw('DISTINCT loan_id'))->get();
	}

	 /**
     * Method to generate all guarantors
     * @return array
     */
    public function getallguarantors()
    {
    	
    	$query = "select distinct

    member_adhersion_id as memberid,
    concat(c.first_name,' ', c.last_name) as names,
    cautionneur_adhresion_id as guarantor,    
    b.operation_type,
	concat(d.first_name,' ', d.last_name) as names_guarantors,
    b.loan_contract,
    left(a.created_at,10) as date,
    amount bonded_amount,
    refunded_amount,
    CASE
       WHEN amount<=refunded_amount THEN 'Relesed'
       ELSE 'Ongoing' END AS release_status,
       
	CASE
        WHEN amount <=refunded_amount THEN  left(a.updated_at,10) END as realesed_date
        
	From member_loan_cautionneurs as a , loans as b ,users c ,users d where a.loan_id=b.id  and a.member_adhersion_id=c.adhersion_id and a.cautionneur_adhresion_id=d.adhersion_id order by b.updated_at
        ";
				
		return DB::select($query);
    }

}
