<?php

namespace Ceb\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Loan extends Model {
    
    // use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at','deleted_at', 'updated_at', 'letter_date'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_regulation' => 'boolean',
    ];
    // fillable
    protected $fillable = [
        'transactionid',
        'loan_contract',
        'adhersion_id',
        'movement_nature',
        'operation_type',
        'letter_date',
        'right_to_loan',
        'wished_amount',
        'loan_to_repay',
        'interests',
        'InteretsPU',
        'amount_received',
        'tranches_number',
        'monthly_fees',
        'cheque_number',
        'bank_id',
        'security_type',
        'cautionneur1',
        'cautionneur2',
        'average_refund',
        'amount_refounded',
        'comment',
        'special_loan_contract_number',
        'remaining_tranches',
        'special_loan_tranches',
        'special_loan_interests',
        'special_loan_amount_to_receive',
        'user_id',
        'status',
        'urgent_loan_interests',
        'factor',
        'rate',
        'reason',
        'is_umergency',
        'emergency_refund',
        'emergency_balance',
    ];

    /**
     * Get the member who were given this loan
     * @return User Object
     */
   public function member()
   {
    return $this->belongsTo('\Ceb\Models\User','adhersion_id','adhersion_id');
   }

   /**
    * Get loan postings
    * @return  
    */
   public function postings()
   {
    return $this->hasMany('\Ceb\Models\Posting','transactionid','transactionid');
   }

   /**
    * Get cautionneurs
    * @return collection
    */
   public function cautions()
   {
        return $this->hasMany('Ceb\Models\MemberLoanCautionneur', 'transaction_id', 'transactionid');
   }

   /**
    * Automatically deleting related rows in Laravel (Eloquent ORM)
    * @return bool
    */
   public function delete()
    {
        // delete all related accounting postings 
        $this->postings()->delete();
        $this->cautions()->delete();
        
        // as suggested by Dirk in comment,
        // it's an uglier alternative, but faster
        // delete the user
        return parent::delete();
    }

    /**
     * Get Non full paid loans(active)
     * @param    $query    
     * @return   
     */
    public function scopeActive($query)
    {
        /**
         * If loan is not fully repaid, then this is active loan
         */
        return $query->where('loan_to_repay','>',  $this->refunds->sum('amount'))
                    ->where('status','approved');

    }



     //create another duplicate of refunds and call it

     public function refundsByContract()

    {
    return $this->hasMany('Ceb\Models\Refund','contract_number','loan_contract');
     }

     ///Then in the function scopeActiveByContract($query){
       

    public function scopeActiveByContract($query)
     {
             return $query->where('loan_to_repay','>',  $this->refundsByContract->sum('amount'))
                    ->where('status','approved');
     }
    


    /**
     * Get Loan with guarantors
     * @param   $query 
     * @return  Builder
     */
    public function scopeWithGuarantors($query)
    {
        return $query->whereIsNotFull('cautionneur1')
                     ->whereIsNotFull('cautionneur2');
    }

    /**
     * Get loan that have most recent refund x months ago
     * @param   $date  
     * @return  collection
     */
    public static function  atRisk()
    {

        $currenDate    = Carbon::now();
        $abandonBefore = Carbon::now()->subMonths(3)->format('Y-m-01');
        $lastYearDate  = $currenDate->subYears(1)->format('Y-m-d');

        // 1. Get active loans
       
      $loans     = collect(DB::select('CALL SP_LOAN_AT_RISK()'));   

     return $loans->transform(function($loan) {
                        $loan->class            = 'bg-gray-300';
                        $loan->risk_level       = 'No Risk';
                        $current                = Carbon::now();


                        // Parse to carbon for better handling of the dates
                        $loan->last_refunded_at = Carbon::parse($loan->last_refunded_at);

                        $monthDifference        = $current->diffInMonths($loan->last_refunded_at);
                        // 6 MONTHS
                        if ($monthDifference >= 6 ) {
                            $loan->class = 'bg-red-300 text-red-900';
                            $loan->risk_level = 'More than 6 months';
                        }
                        // 4-6 months
                        if ($monthDifference >= 4 && $monthDifference < 6 ) {
                            $loan->class = 'bg-orange-300 text-orange-900';
                            $loan->risk_level = 'Between 4-6 months';
                        }

                        // less than 3 months
                        if ($monthDifference <= 3 ) {
                            $loan->class = 'bg-yellow-300 text-yellow-900';
                            $loan->risk_level = 'Below 3 months';
                        }

                        $loan->last_refunded_at = $loan->last_refunded_at->format('Y-m-d');
                        return $loan;
                    });
    }

    /**
     * Overwrite database balance column because 
     * of the new business rules that does not
     * distinguish payments of the emergency
     * @return number
     */
    public function getEmergencyBalanceAttribute()
    {
        return $this->balance();
    }

    /**
     * Calculating remaining loan payment installments
     * @return numeric
     */
    public function  getRemainingInstallmentsAttribute()
    {
        $refundedAmount = $this->refunds->sum('amount');
       
        // Avoid zero division Error, If we have no Refund
        // Then all tranches are still intact.
        if ($refundedAmount <= 0) {

            return $this->tranches_number;
        }
         
        // We have refund, let's proceed with calculating 
        // Remaining balance based on the amount refunded
    
        $remainings = ($this->balance() > 0 ) ? 
                      $this->balance() / $refundedAmount:
                      0;

        // Avoid decimals for installments
        if ($remainings > 0 && $remainings < 1) {
            $remainings = 1;
        }
        return intval($remainings);
    }

    /**
     * Overwrite database balance column because 
     * of the new business rules that does not
     * distinguish payments of the emergency
     * @return number
     */
    public function  getEmergencyRefundAttribute()
    {
            return $this->refunds->sum('amount');
    }

    /**
     * Get cautionneur 1
     * @return Collection
     */
    public function getCautionneur1Attribute()
    {
        return $this->cautions()->get()->first();
    }

    /**
     * Get cautionnuer 2
     * @return Collection 
     */
    public function getCautionneur2Attribute()
    {
        return $this->cautions()->get()->last();
    }

    /**
     * Get calculated monthly fees attribute
     * @return numeric
     */
    public function getCalculatedMonthlyFeeAttribute()
    {
    if (trim(strtolower($this->operation_type))=='special_loan') {
        try
        {
         $previous = \DB::select("SELECT * FROM loans WHERE status='approved' AND is_umergency = 0 AND id < ? order by id desc LIMIT 1", [$this->id]);

            return $this->monthly_fees - $previous[0]->monthly_fees;
        }
        catch(\Exception $ex)
        {
            Log::critical($ex->getMessage());
        }
        }

        return $this->monthly_fees;
    }

    /**
     * Refunds done to this loan
     * @return object 
     */
    public function refunds()
    {
        return $this->hasMany('Ceb\Models\Refund','loan_id','id');
    }
    
    /**
     * Get Loan balance
     *
     * @return numeric 
     */
    public function balance()
    {
        return $this->loan_to_repay - $this->refunds()->sum('amount');
    }

    /**
     * Get balance 
     * 
     * @return 
     */
    public function getBalanceAttribute()
    {
        return $this->balance();
    }

    /**
     * Determine if this loan is full paid
     *
     * @return bool
     */
    public function isFullPaid()
    {
        return $this->balance() <= 0;
    }

    /**
     * Scope to determine if the loan is full paid
     * 
     * @return ;
     */
    public function scopeIsFullPaid()
    {
        return $this->balance() <= 0;
    }
    /** 
     * Get refund by adhersion ID
     *
     * @return Object
     */
    public function refundsByAdhersion()
    {
        return $this->hasMany('Ceb\Models\Refund','adhersion_id','adhersion_id');
    }

    /**
     * Find loan by transaction id
     * @param  query scope $query     
     * @param  string $transactionId 
     * @return this
     */
    public function scopeFindByTransaction($query,$transactionId)
    {
        return $query->where('transactionid','=',$transactionId);
    }

    /**
     * Scope a query to only include users of a given type(ordinary loan/ urgent , special etc...).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('operation_type', $type);
    }




     /** Scope a query to only include rejected loans.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeofCreated($query)

    {
        $ldate = date('Y');
        return $query->whereYear('created_at','=',$ldate);
    }


    /**
     * Get ordinary loan
     * @param  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsOrdinary($query)
    {
        return $query->where('operation_type','LIKE','%ordinary_loan')
                     ->where('status','approved');
    }


    /**
     * Get ordinary loan
     * @param  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsNotReliquant($query)
    {
        return $query->where('operation_type','<>','loan_relicat')
                     ->where('status','approved');
    }

    /**
     * Get emergency loan
     * @param  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getEmergencyMonthlyFeeAttribute()
    {
        /** Make sure this is a valid emergency loan before proceeding */
        if ($this->operation_type == 'emergency_loan' && $this->emergency_balance > 0) {
            try
            {
                return $this->loan_to_repay / $this->tranches_number ;
            }
            catch (\Exception $e){
                return $this->monthly_fees;
            }
        }

        return 0;
    }

    /**
     * Get emergency loan
     * @param  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsUmergency($query)
    {
        return $query->where('operation_type','emergency_loan')
                     ->where('is_umergency',1)
                     ->where('status','approved');
    }

    /**
     * Get emergency loan
     * @param  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsNotUmergency($query)
    {
        return $query->where('operation_type','<>','emergency_loan')
                     ->where('is_umergency',0);
    }

    /**
     * Confirm if the loan is emergency
     * @return boolean
     */
    public function getIsEmergencyLoanAttribute()
    {
        return TRIM($this->operation_type)  == 'emergency_loan';
    }

    /**
     * Get paid emergency loan
     * @param  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsPaidUmergency($query)
    {
        return $query->where('operation_type','LIKE','emergency_loan')
                     ->where('is_umergency',1)
                     ->where('emergency_balance',0)
                     ->where('status','approved');
    }
    
    /**
     * Get not paid emergency loan
     * @param  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsNotPaidUmergency($query)
    {
        return $query->where('operation_type','LIKE','emergency_loan')
                     ->where('is_umergency',1)
                     // Refund are not enough to cover for this loan
                     ->whereRaw('loan_to_repay > (SELECT CASE WHEN sum(amount) IS NULL THEN 0 ELSE sum(amount) END from refunds where contract_number=loan_contract)') 
                     ->where('status','approved');
    }
    
    /**
     * Loan that includes relicat
     * @param   $query 
     * @return  
     */
    public function scopeLoanWithRelicat($query)
    {
          // 1. Get all loans based on their contracts 
          $loanContracts = $query->select('loan_contract')->lists('loan_contract')->toArray();

          if(empty($loanContracts)){
            return collect($loanContracts);
          }

          // 2. Get actual sum based on contract
          $loans = DB::unprepared(
                    DB::raw("
                        DROP TABLE IF EXISTS temp_loans, temp_pending_emergency_with_relicat;
                        CREATE TEMPORARY TABLE temp_loans(
                                SELECT
                                    loan_contract,
                                    adhersion_id,
                                    max(monthly_fees) monthly_fees,
                                    SUM(loan_to_repay) loan_to_repay
                                FROM loans
                                WHERE 
                                    loan_contract IN (".implode(",",$loanContracts).")
                                GROUP BY loan_contract);
                        
                       CREATE TEMPORARY TABLE temp_pending_emergency_with_relicat(
                        SELECT 
                            temp_loans.*,
                            (SELECT CASE WHEN sum(amount) IS NULL THEN 0 ELSE sum(amount) END 
                             FROM refunds where contract_number = loan_contract)  refund_amount
                        FROM temp_loans 
                        WHERE 
                        loan_to_repay > 
                        (SELECT CASE WHEN sum(amount) IS NULL THEN 0 ELSE sum(amount) END from refunds where contract_number = loan_contract)
                        );
                    ")
                );

          return collect(DB::table('temp_pending_emergency_with_relicat')->get())->transform(function($loan){
               $loan->loan_to_repay =  (float) $loan->loan_to_repay;
               $loan->refund_amount =  (float) $loan->refund_amount;
               $loan->monthly_fees  =  (float) $loan->monthly_fees;               
              // Calculate Balance 
              $loan->emergency_balance = $loan->loan_to_repay - $loan->refund_amount;
              return $loan;
          });
    }

    /**
     * Get loan, that still has more right to loan
     * @param  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasMoreRightToLoan($query)
    {
        return $query->where('loan_to_repay','<','right_to_loan');
    }

    /**
     * Get right to loan attribute
     * @return  
     */
    public function getRemainingRightToLoanAttribute()
    {
        return $this->right_to_loan - $this->loan_to_repay;
    }
    
    /**
     * Scope a query to only include loans of a given status.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }


    /**
     * Scope a query to only include loans of a given status.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
     public function scopeIsNotRelicat($query)
    {
        return $query->where('movement_nature','<>','remainers');
    }
    
    /**
     * Scope a query to only include approved loans.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', '=','approved');
    }

    /**
     * Scope a query to only include pending loans.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', '=','pending');
    }

    /**
     * Get loan by transaction ID
     * @param  $query         
     * @param  $transactionId 
     * @return mixed
     */ 
    public function scopeByTransaction($query,$transactionid)
    {
        return $query->where('transactionid',$transactionid);
    }
    
    /**
     * Scope a query to only include rejected loans.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('status', '=','rejected');
    }
    



     /**
     * Scope a query to only get blocked loans, which are considered if there is no bank or cheque.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlocked($query)
    {
        return $query->where('status','pending');
    }

     /**
     * Scope a query to only get unblocked loans, which are considered if there is no bank or cheque.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnBlocked($query)
    {
        return $query->where('status','unblocked');
    }

    /**
     * Count paid loans
     * @return number;
     */
    public function countPaid()
    {
        $count=  DB::select("SELECT count(1) as count
                            FROM 
                                (SELECT adhersion_id,sum(loan_to_repay) loan FROM loans where status = 'approved' group by adhersion_id) 
                                    as a
                            LEFT JOIN 
                                (SELECT adhersion_id,sum(amount) refund FROM refunds group by adhersion_id) 
                                   as b
                            ON a.adhersion_id = b.adhersion_id 
                            WHERE a.loan <= b.refund
                        ");

        return array_shift($count)->count;
    }

    /**
     * Count loan with outstanding money
     * @return number;
     */
    public function countOutStanding()
    {
        $count=  DB::select("SELECT count(1) as count
                            FROM 
                                (SELECT adhersion_id,sum(loan_to_repay) loan FROM loans where status = 'approved' group by adhersion_id) 
                                    as a
                            LEFT JOIN 
                                (SELECT adhersion_id,sum(amount) refund FROM refunds group by adhersion_id) 
                                   as b
                            ON a.adhersion_id = b.adhersion_id
                            WHERE a.loan > b.refund
                        ");

        return array_shift($count)->count;
    }

      /**
     * Sum  part social
     * @return number;
     */
    public function sumPartSocial()
    {  
       
        $sum=  DB::select("SELECT CREDIT.amount_credit - DEBIT.amount_debit as balance FROM 
(select sum(amount) amount_credit
                                FROM 
                                   postings where account_id='1'
                                   and  transaction_type ='credit') as CREDIT ,
                                   
(SELECT sum(amount) amount_debit
                                FROM 
                                   postings where account_id='1'
                                   and  transaction_type ='debit') as DEBIT;
                           ");
        
        return array_shift($sum)->balance;
    }
    /**
     * Sum  outstanding loan amount
     * @return number;
     */
    public function sumOutStanding()
    {
        $sum=  DB::select("SELECT sum(a.loan_to_repay) - sum(b.amount) as amount
                                FROM 
                                    (select sum(loan_to_repay) as loan_to_repay FROM loans WHERE status = 'approved') as a,
                                    (select sum(amount) as amount FROM refunds ) as b 
                           ");
        
        return array_shift($sum)->amount;
    }

    /**
     * Get members with loans
     * @param  string $institition 
     * @return array 
     */
    public function memberWithLoans($institition,array $status = ['actif','inactif'])
    {
        return collect(DB::select("CALL SP_MEMBERS_WITH_LOANS({$institition}, '".implode(",",$status)."')"));   
    }

    /**
     * Get member loan balance
     * @return 
     */
    public function getMembersLoanBalance($endDate)
    {
        DB::statement('DROP TABLE IF EXISTS TEMP_MEMBER_LOAN_REFUND');
        DB::statement('CREATE TEMPORARY TABLE TEMP_MEMBER_LOAN_REFUND(
                        SELECT a.adhersion_id,
                              CASE WHEN sum_refund is null THEN sum_loan ELSE sum_loan  - sum_refund end as balance FROM
                        (
                            SELECT adhersion_id,sum(loan_to_repay) as sum_loan FROM 
                                loans as a where date(created_at) <= \''.$endDate.'\'  and a.status=\'approved\'  group by adhersion_id) as a
                            LEFT JOIN 
                        (
                            SELECT adhersion_id,case when sum(amount) is null then 0 else sum(amount) end as sum_refund 
                                FROM refunds where date(created_at) <=\''.$endDate.'\'  group by adhersion_id) as b 
                            ON a.adhersion_id = b.adhersion_id
                        );'
                    );

        $query = 'SELECT a.adhersion_id,balance,first_name,last_name,service,c.name institution,status
                  FROM TEMP_MEMBER_LOAN_REFUND as a
                  LEFT JOIN users as b on a.adhersion_id = b.adhersion_id
                  LEFT JOIN institutions as c on b.institution_id = c.id   WHERE balance <>0 ';

        $results = DB::select(DB::raw($query));
        return new Collection($results);
    }
 

     /**
      * Get Higher contribution Loan
      * @return  
      */
     public function getHigherLoanThanContribution()
     {
      return collect(DB::select('CALL SP_BALANCE_HIGH_SAVINGS()'));   
     }

    /**
     * Get member loan balance via  dashboard
     * @return 
     */
    public function getMembersLoanBalancedashbord()
     {

    
        DB::statement('DROP TABLE IF EXISTS TEMP_MEMBER_LOAN_REFUND');
        DB::statement('CREATE TEMPORARY TABLE TEMP_MEMBER_LOAN_REFUND(
                        SELECT a.adhersion_id,
                              CASE WHEN sum_refund is null THEN sum_loan ELSE sum_loan  - sum_refund end as balance FROM
                        (
                            SELECT adhersion_id,sum(loan_to_repay) as sum_loan FROM 
                                loans where  status=\'approved\'  group by adhersion_id) as a
                            LEFT JOIN 
                        (
                            SELECT adhersion_id,case when sum(amount) is null then 0 else sum(amount) end as sum_refund 
                                FROM refunds group by adhersion_id) as b 
                            ON a.adhersion_id = b.adhersion_id
                        );'
                    );

        $query = 'SELECT a.adhersion_id,balance,first_name,last_name,service,c.name institution ,status
                  FROM TEMP_MEMBER_LOAN_REFUND as a
                  LEFT JOIN users as b on a.adhersion_id = b.adhersion_id
                  LEFT JOIN institutions as c on b.institution_id = c.id   WHERE balance <>0 ';

        $results = DB::select(DB::raw($query));
        return new Collection($results);
    }


    /**
     * Get loan records per adhersion_id
     * @param  string $adhersion_id 
     * @return array             
     */
    public function getLoanRecords($adhersion_id =null,$startDate=NULL, $endDate=NULL)
    {
         $startDate = empty($startDate) ? '2019-01-01' : $startDate;
         $endDate   = empty($endDate) ? date('Y-m-d') : $endDate;
         $results   = collect(DB::select('call SP_LOAN_RECORDS(?)',array($adhersion_id)));

         // Filter only for dates specified
         $loans   = $results->filter(function($loan) use ($startDate,$endDate){
                        $createdAt = date('Y-m-d',strtotime($loan->created_at));
                        return $createdAt >= $startDate && $createdAt <= $endDate;
                     });

         // Get Balance from previous Loan
         $previousLoan = $results->filter(function($loan) use ($startDate,$endDate){
                        return date('Y-m-d',strtotime($loan->created_at)) < $startDate;
                     });

        $previousBalance = $previousLoan->sum('loan_amount') - $previousLoan->sum('tranches');
        
        return collect(['loans' => $loans, 'previous_blance' => $previousBalance]);
    }


    public function scopeLoanRecords($query,$startDate=NULL, $endDate=NULL)
    {
         $startDate = empty($startDate) ? '2007-01-01' : $startDate;
         $endDate   = empty($endDate) ? date('Y-m-d') : $endDate;

         return $query->with('refunds')->approved()
                       ->whereBetween(DB::raw('created_at'),[$startDate,$endDate])
                       ->get();
    }



     /** all loan during year **/
    public function getLoanOfYear()
    {

    $query = DB::select("SELECT a.adhersion_id,b.first_name,b.last_name,c.name,operation_type,left(a.created_at,10) created_at,loan_to_repay loan 
                 FROM loans a , users b ,institutions c
                where a.adhersion_id=b.adhersion_id and b.institution_id=c.id and a.status = 'approved' and operation_type not in ('loan_relicat','emergency_loan') and loan_to_repay<>'0'  and left(a.created_at,4)='2020'
                     order by left(a.created_at,10)");

     /// $results = DB::select(DB::raw($query));
     return new Collection($query);
    }


     /** all loan during year **/
    public function getEmergencyOfYear()
    {

    $query = DB::select("SELECT a.adhersion_id,b.first_name,b.last_name,c.name,operation_type,left(a.created_at,10) created_at,loan_to_repay loan 
                 FROM loans a , users b ,institutions c
                where a.adhersion_id=b.adhersion_id and b.institution_id=c.id and a.status = 'approved' and operation_type ='emergency_loan'  and left(a.created_at,4)='2020'
                     order by left(a.created_at,10)");

     /// $results = DB::select(DB::raw($query));
     return new Collection($query);
    }
}

  