<?php

namespace Ceb\Models;

use Ceb\Models\User;
use Ceb\Models\Loan;

use Illuminate\Support\Facades\DB;

class Refund extends Model {

	protected $fillable = [
		'adhersion_id',
		'contract_number',
		'month',
		'amount',
		'tranches_number',
		'transaction_id',
		'member_id',
		'wording',
		'user_id',
		'loan_id',
		];

  /**
    * Get loan postings
    * @return  
    */
   public function postings()
   {
		return $this->hasMany(Posting::class,'transactionid','transaction_id');
   }

   /**
    * Refund Member
    * @return  
    */
   public function member()
   {
		return $this->hasMany(User::class,'adhersion_id','adhersion_id');
   }
	/**
	 * Get loan by which this Refund belongs to
	 * 
	 * @return object
	 */
	public function loan()
	{
		return $this->belongsTo(Loan::class,'loan_id','id');
	}

	   /**
	    * Automatically deleting related rows in Laravel (Eloquent ORM)
	    * @return bool
	    */
	   public function delete()
	    {
	        // delete all related accounting postings 
	        $this->postings()->delete();
	        return parent::delete();
	    }
	/**
	 *
	 * Get Refund by Transaction ID
	 * @param   $query         
	 * @param   $transactionid 
	 * @return  
	 */
	public function scopeByTransaction($query,$transactionid)
	{
		return $query->where('transaction_id',$transactionid);
	}
	/** 
	 * Get refund by adhersion ID
	 *
	 * @return Object
	 */
	public function loanByAdhersion()
	{
		return $this->hasMany(Loan::class,'adhersion_id','adhersion_id');
	}

	/**
     * Sum  outstanding loan amount
     * @return number;
     */
    public function sumRefunds()
    {
    	$sum=  DB::select("select sum(amount) as amount FROM refunds");
    	
     	return array_shift($sum)->amount;
    }

    /**
     * Get Octroye
     * @param  $startDate 
     * @param   $endDate   
     * @return  array
     */
    public static function octroye($startDate,$endDate,$institution)

    {   // GET LOANS 

    	$conditions = " c.id = ".$institution; 

        DB::statement('DROP TABLE IF EXISTS TEMP_MEMBER_LOAN_REFUND');
        DB::statement('CREATE TEMPORARY TABLE TEMP_MEMBER_LOAN_REFUND(
                        SELECT a.adhersion_id,a.loan_contract,a.operation_type,
                              CASE WHEN sum_refund is null THEN sum_loan ELSE sum_loan  - sum_refund end as balance FROM
                        (
                            SELECT adhersion_id,loan_contract,a.operation_type,sum(loan_to_repay) as sum_loan FROM 
                                loans as a where date(created_at) <= \''.$endDate.'\'  and a.status=\'approved\'  group by adhersion_id) as a
                            LEFT JOIN 
                        (
                            SELECT adhersion_id,contract_number as loan_contract,case when sum(amount) is null then 0 else sum(amount) end as sum_refund 
                                FROM refunds where date(created_at) <=\''.$endDate.'\'  group by adhersion_id) as b 
                            ON a.adhersion_id = b.adhersion_id
                        );'
                    );

        $query = "SELECT distinct a.adhersion_id,a.loan_contract,a.operation_type,balance,first_name,last_name,service,c.name institution,left(d.created_at,7) as fin_dette
                  FROM TEMP_MEMBER_LOAN_REFUND as a
                  LEFT JOIN users as b on a.adhersion_id = b.adhersion_id
                  LEFT JOIN institutions as c on b.institution_id = c.id   
                  LEFT JOIN refunds as d on a.adhersion_id=d.adhersion_id WHERE $conditions and   balance<= 0 
                  and d.created_at between '$startDate' and '$endDate'";
             

        $results = DB::select(DB::raw($query));
        return $results;

        }

	 /**
	     * Modified Refunds
	     * @param  $startDate 
	     * @param   $endDate   
	     * @return  array
	     */
	    public static function modifiedRefund($startDate,$endDate)
	    {
	    	// GET LOANS 
	    	DB::statement('DROP TABLE IF EXISTS TEMP_LOANS_PER_MEMBER');
	    	DB::statement("CREATE TEMPORARY TABLE TEMP_LOANS_PER_MEMBER
							(
								 SELECT
								    date(created_at) as date,
									adhersion_id,
                            		monthly_fees,
                            		operation_type
								 FROM loans 
							     WHERE STATUS ='approved' AND date(created_at) BETWEEN ? AND ?
							)",[$startDate,$endDate]);

	        // GET THE REPORT
	        $result = DB::select("SELECT  CASE 
									WHEN c.first_name IS NULL THEN c.last_name
						            WHEN c.last_name  IS NULL THEN c.first_name
								ELSE CONCAT(c.first_name,' ',c.last_name) END AS names,
								    d.name as institution,
							        a.adhersion_id,
							        a.operation_type,
							        a.monthly_fees,
							        a.date
								FROM TEMP_LOANS_PER_MEMBER AS a
						        LEFT JOIN  users AS c ON a.adhersion_id = c.adhersion_id
						         LEFT JOIN institutions as d on c.institution_id = d.id where  a.monthly_fees<>'0'");
	        return $result;
	    }
    /**
     * Get Latest payments of the loan
     * @param  startdate $startDate 
     * @param  $endDate   
     * @return  
     */
    public static function refundLatestPayment(){
    	/** GETTING ALL MEMBERS LOANS **/
    	DB::statement('DROP TABLE IF EXISTS TEMP_memberloans');
		DB::statement('CREATE TEMPORARY TABLE TEMP_memberloans
						(
						SELECT 
							adhersion_id,
							sum(loan_to_repay) loansAmount,
							monthly_fees
						 FROM loans 
						 WHERE `status` = \'approved\' 
						 GROUP BY adhersion_id
					);');

		/** GETTING ALL MEMBERS REFUNDS **/
		DB::statement('DROP TABLE IF EXISTS TEMP_memberrefunds');
		DB::statement('CREATE TEMPORARY TABLE TEMP_memberrefunds
						(
						 SELECT adhersion_id,
						         sum(amount) refundedAmount 
						 FROM refunds GROUP BY adhersion_id
						);'
					);

		/** GETTING MEMBER WITH LOANS **/
		DB::statement('DROP TABLE IF EXISTS TEMP_members_with_active_loans');
		DB::statement('CREATE TEMPORARY TABLE TEMP_members_with_active_loans
						(
						 SELECT a.adhersion_id,
						     loansAmount,
						     refundedAmount,
						     first_name,last_name,
						     service,
						     (loansAmount - refundedAmount) as balance,
						     monthly_fees FROM TEMP_memberloans AS a,
						 TEMP_memberrefunds as b,
						 users c WHERE  a.loansAmount > b.refundedAmount 
						 AND a.adhersion_id = b.adhersion_id
						 AND  a.adhersion_id = c.adhersion_id
						 AND (loansAmount - refundedAmount) <= monthly_fees
						);'
					);

		$query = 'SELECT * FROM TEMP_members_with_active_loans';
		return DB::select(DB::raw($query));
    }

    /**
     * Get Iregularities
     * @param  integer $months
     * @return 
     */
    public function refundIrregularities($yearMonth = 3)
    {
    	$date  = $yearMonth . '-31';
    	$month = (int) substr($yearMonth,-2);
    	$year  = substr($yearMonth,0, 4);
    	/** GETTING ALL MEMBERS LOANS **/
    	DB::statement('DROP TABLE IF EXISTS TEMP_memberloans');
		DB::statement('CREATE TEMPORARY TABLE TEMP_memberloans
						(
							SELECT adhersion_id,sum(loan_to_repay) loansAmount FROM loans where `status` = \'approved\' group by adhersion_id
						);');


		/** GETTING ALL MEMBERS REFUNDS **/
		DB::statement('DROP TABLE IF EXISTS TEMP_memberrefunds');
		DB::statement('CREATE TEMPORARY TABLE TEMP_memberrefunds
						(
						SELECT adhersion_id,sum(amount) refundedAmount FROM refunds group by adhersion_id
						);'
					);
		/** GETTING MEMBER WITH LOANS **/
		DB::statement('DROP TABLE IF EXISTS TEMP_members_with_active_loans');
		DB::statement('CREATE TEMPORARY TABLE TEMP_members_with_active_loans
						(
						 SELECT a.adhersion_id,loansAmount,refundedAmount,first_name,last_name,service FROM TEMP_memberloans AS a,
						 TEMP_memberrefunds as b,
						 users c WHERE  a.loansAmount > b.refundedAmount AND a.adhersion_id = b.adhersion_id
						 AND  a.adhersion_id = c.adhersion_id
						);'
					);

		DB::statement('DROP TABLE IF EXISTS TEMP_member_latest_refund');
		DB::statement('CREATE TEMPORARY TABLE TEMP_member_latest_refund
						(
						SELECT * FROM
									(
									SELECT 
											adhersion_id,
									        max(created_at) as last_date
											FROM refunds 
											GROUP BY 
											adhersion_id
									) AS a
									WHERE 
									/* Smaller or equal than x month ago */
									last_date <= ?
						);'
					,[$date]);

		DB::statement('DROP TABLE IF EXISTS TEMP_member_latest_loan');
		DB::statement('CREATE TEMPORARY TABLE TEMP_member_latest_loan
						(
						SELECT a.adhersion_id, a.monthly_fees,a.comment FROM loans as a,
						(SELECT * FROM
									(
									SELECT 
											adhersion_id,
									        max(id) as id
											FROM loans 
						                    WHERE `status` =\'approved\'
											GROUP BY 
											adhersion_id
									) AS a
						 ) as b
						 WHERE a.id = b.id AND a.adhersion_id = b.adhersion_id
						);'
					);

		DB::statement('DROP TABLE IF EXISTS TEMP_contributions');
		DB::statement('CREATE TEMPORARY TABLE TEMP_contributions
						(
								SELECT a.adhersion_id,
								   CASE 
									WHEN withdrawal_amount IS NULL THEN contributed_amount
                                    ELSE contributed_amount - withdrawal_amount 
                                    END AS contributed_amount,
								    a.last_date
                                   FROM  (
                                       SELECT 
											adhersion_id,
									        sum(amount) as contributed_amount,
									        max(created_at) as last_date
											FROM contributions 
									        WHERE transaction_type = \'saving\'
												GROUP BY 
											adhersion_id) as a
								LEFT JOIN
								(
                                 SELECT adhersion_id,
									    sum(amount) as withdrawal_amount 
								 FROM contributions WHERE transaction_type = \'withdrawal\'
								) as b 
                                ON a.adhersion_id=b.adhersion_id
						);');


		$query = 'SELECT a.adhersion_id,
								       a.first_name,
								       a.last_name,
								       a.service,
								       loansAmount,
								       refundedAmount,
								       b.last_date,
								       c.monthly_fees,
								       (SELECT sum(amount) FROM refunds WHERE adhersion_id = a.adhersion_id AND CAST(RIGHT(month,2) AS UNSIGNED) = ? AND  LEFT(created_at,4) = ? ) 
								       AS paid_amount,
								       c.comment,
								       d.contributed_amount 
								FROM TEMP_members_with_active_loans as a,
								 TEMP_member_latest_refund as b,
								 TEMP_member_latest_loan c,
								 TEMP_contributions as d
								WHERE   a.adhersion_id = b.adhersion_id 
								AND   b.adhersion_id = c.adhersion_id 
								AND  b.adhersion_id = d.adhersion_id';
							
		return DB::select(DB::raw($query),[$month,$year]);
    }
    
}
