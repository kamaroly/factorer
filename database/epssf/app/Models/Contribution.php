<?php

namespace Ceb\Models;

use Ceb\Traits\EloquentDatesTrait;
use Illuminate\Support\Facades\DB;

class Contribution extends Model {
    

    protected $fillable = [
        'adhersion_id',
        'institution_id',
        'month',
        'amount',
        'state',
        'transactionid',
        'year',
        'contract_number',
        'transaction_type',
        'transaction_reason',
        'wording',
        'charged_amount',
        'charged_percentage',
    ];


    /**
     * Relationship with member
     * @return Ceb\Models\User
     */
    public function member()
    {
        return $this->belongsTo('Ceb\Models\User','adhersion_id','adhersion_id');
    }
        /**
     * Relationship with member
     * @return Ceb\Models\User
     */
    public function institution()
    {
        return $this->belongsTo('Ceb\Models\Institution');
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
    * Automatically deleting related rows in Laravel (Eloquent ORM)
    * @return bool
    */
   public function delete(){
    
        // delete all related accounting postings 
        $this->postings()->delete();
        return parent::delete();
    }
    
    /**
     * Get transactionType
     * @param  $query
     * @param  $date 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfTransactionType($query,$transactionType)
    {
        return $query->where('transaction_type',$transactionType);
    }


    /**
     * Get Contribution by transaction ID
     * @param  $query         
     * @param  $transactionId 
     * @return mixed
     */ 
    public function scopeByTransaction($query,$transactionid)
    {
        return $query->where('transactionid',$transactionid);
    }

    /**
     * Get transactionType of saving
     * @param  $query
     * @param  $date 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsSaving($query)
    {
        return $query->where('transaction_type','saving');
    }


        /**
     * Get transactionType of saving for count checking  yearly 
     * @param  $query
     * @param  $date 
     * @return \Illuminate\Database\Eloquent\Builder
     */
   
    public function scopeIsSavingcount($query)
    {
        return $query->where('transaction_type','saving')
                      ->whereIn('transaction_reason',['Montly_contribution','individual_monthly_contribution']);
                      
    }

    /**
     * Get transactionType of saving
     * @param  $query
     * @param  $date 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsWithdrawal($query)
    {
        return $query->where('transaction_type','withdrawal');
    }

    /**
     * Get record after a given id
     * @param  $query
     * @param  $date 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFor($query,$adhersion_id)
    {
        return $query->where('adhersion_id',$adhersion_id);
    }

    /**
     * Get current Year data
     * @param   $query 
     * @return   query
     */
    public function scopeCurrentYear($query)
    {
        return $query->where(DB::raw('YEAR(created_at)'),date('Y'));
    }
 
    /**
     * Get members with annual interest
     * @param  builder $query 
     * @param  string $year  year
     * @return this
     */
    public function scopeReceivedInterestFor($query,string $year)
    {
        return $query->where('transaction_reason','ANNUAL_INTEREST_'.$year)
                     ->whereTransactionType('saving');
    }

    /**
     * Get members who withdrew interest for x Year
     * @param  builder $query 
     * @param  string $year  year
     * @return this
     */
    public function scopeWithdrewInterestFor($query,string $year)
    {
        return $query->where('transaction_reason','BULK_WITHDRAW_'.$year)
                     ->whereTransactionType('withdrawal');
    }

    /**
     * Get members who can get Annual interest for Year
     * @param  builder $query 
     * @param  string $year  year
     * @return this
     */
    public function scopeCanReceivedInterestFor($query,string $year)
    {
        // 1. Get first all people with interest for this Year
        $receivedInterest = $this->receivedInterestFor($year)->get();

        // 2. Get all members who have withdrew interest for this year
        $withdrewInterest = $this->withdrewInterestFor($year)->get();

        // 3. Remove those who received their interest
        $eligibleMembers  = $receivedInterest->filter(function($member) use($withdrewInterest){
            return $withdrewInterest->where('adhersion_id',$member->adhersion_id)->count() > 0;
        });

        // 4. Return the results
        return $query->receivedInterestFor($year)->whereIn('adhersion_id',$eligibleMembers)->pluck('adhersion_id');
    }
    /**
     * Method to generate niveaut d'epargne report
     * @return array
     */
    public function savingLevel(array $status = [],$yearMonthDay)
    {
        // Add status conditions if provided
        $userCondition = '';
        if(!empty($status)){
            $status        = implode("','",$status);

            $userCondition = " and a.status IN ('$status')";
            $dateCondition = " date(created_at) <=('$yearMonthDay')";
        }


        $query = "
                SELECT  a.adhersion_id,a.first_name,a.last_name,a.institution,a.service,amount as savings,
                a.status FROM 
                (SELECT a.adhersion_id,a.first_name,a.last_name,b.name as institution,service,status FROM users as a,
                institutions b where a.institution_id = b.id $userCondition and a.adhersion_id<>'200799999' and a.service is not null) as a
                    LEFT JOIN 
                (
                SELECT savings.adhersion_id, 
                    case when withdrawal.amount is null then savings.amount
                    else savings.amount - withdrawal.amount end as amount 
                FROM
                 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type ='saving'
                 and  $dateCondition group by adhersion_id ) as savings
                  LEFT JOIN
                 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type ='withdrawal' 
                 and  $dateCondition group by adhersion_id  ) as withdrawal
                 ON savings.adhersion_id = withdrawal.adhersion_id
                 )
                 c ON a.adhersion_id = c.adhersion_id";
                
        return DB::select($query);
    }


    /**
     * Method to generate niveaut d'epargne for  dashborad without enddate
     * @return array
     */
    public function savingLeveldashboard(array $status = [])
    {
        // Add status conditions if provided
        $userCondition = '';
        if(!empty($status)){
            $status        = implode("','",$status);

            $userCondition = " and a.status IN ('$status')";
       
        }


        $query = "
                SELECT  a.adhersion_id,a.first_name,a.last_name,a.institution,a.service,amount as savings,
                a.status FROM 
                (SELECT a.adhersion_id,a.first_name,a.last_name,b.name as institution,service,status FROM users as a,
                institutions b where a.institution_id = b.id $userCondition and a.adhersion_id<>'200799999' and a.service is not null) as a
                    LEFT JOIN 
                (
                SELECT savings.adhersion_id, 
                    case when withdrawal.amount is null then savings.amount
                    else savings.amount - withdrawal.amount end as amount 
                FROM
                 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type ='saving'
                  group by adhersion_id ) as savings
                  LEFT JOIN
                 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type ='withdrawal' 
                  group by adhersion_id  ) as withdrawal
                 ON savings.adhersion_id = withdrawal.adhersion_id
                 )
                 c ON a.adhersion_id = c.adhersion_id";
                
        return DB::select($query);
    }
      
      
       /**
     * Method to generate all members report
     * @return array
     */
      public function savingLevelMember()
    {
        $query = "
                SELECT  a.adhersion_id,a.first_name,a.last_name,a.institution,a.service,amount as savings,a.status  FROM 
                (SELECT a.adhersion_id,a.first_name,a.last_name,b.name as institution,service FROM users as a,
                institutions b where a.institution_id = b.id and a.adhersion_id<>'200799999' and a.service is not null) as a
                    LEFT JOIN 
                
                (
                SELECT savings.adhersion_id, 
                    case when withdrawal.amount is null then savings.amount
                    else savings.amount - withdrawal.amount end as amount 
                FROM
                 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type ='saving' group by adhersion_id) as savings
                  LEFT JOIN
                 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type ='withdrawal' group by adhersion_id) as withdrawal
                 ON savings.adhersion_id = withdrawal.adhersion_id
                 )
                 c ON a.adhersion_id = c.adhersion_id;
                ";

        return DB::select($query);
    }

         /**
     * Method to generate all institution list report
     * @return array
     */
      public function getinstitutionList()
    {
        $query = "
                SELECT id,name,created_at from institutions;
                ";

        return DB::select($query);
    }

     /**
     * Method to generate left members report
     * @return array
     */
      public function savingleftMember()
    {   
        $query = "
                SELECT  a.adhersion_id,a.first_name,a.last_name,a.institution,a.service,a.status,amount as savings FROM 
                (SELECT a.adhersion_id,a.first_name,a.last_name,b.name as institution,service,a.status FROM users as a,
                institutions b where a.institution_id = b.id and a.status='left')  as a
                    LEFT JOIN 
                
                (
                SELECT savings.adhersion_id, 
                    case when withdrawal.amount is null then savings.amount
                    else savings.amount - withdrawal.amount end as amount 
                FROM
                 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type ='saving' group by adhersion_id) as savings
                  LEFT JOIN
                 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type ='withdrawal' group by adhersion_id) as withdrawal
                 ON savings.adhersion_id = withdrawal.adhersion_id
                 )
                 c ON a.adhersion_id = c.adhersion_id;
                ";

        return DB::select($query);
    }

    public function newMember()
    {   
        $query = "
                SELECT  a.adhersion_id,a.first_name,a.last_name,a.institution,a.service,a.created_at,a.status,amount as savings FROM 
                (SELECT a.adhersion_id,a.first_name,a.last_name,b.name as institution,service,a.created_at,a.status FROM users as a,
                institutions b where a.institution_id = b.id and a.status='actif' and a.created_at>= '2021-01-01 00:00:00')  as a
                    LEFT JOIN 
                
                (
                SELECT savings.adhersion_id, 
                    case when withdrawal.amount is null then savings.amount
                    else savings.amount - withdrawal.amount end as amount 
                FROM
                 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type ='saving' group by adhersion_id) as savings
                  LEFT JOIN
                 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type ='withdrawal' group by adhersion_id) as withdrawal
                 ON savings.adhersion_id = withdrawal.adhersion_id
                 )
                 c ON a.adhersion_id = c.adhersion_id;
                ";

        return DB::select($query);
    }
  public function IndividualyYeatInterest()
    {   
        $query = " SELECT  distinct a.adhersion_id,a.first_name,a.last_name,b.name as institution,a.service,c.created_at,a.status,c.transaction_type as transaction_type,c.transaction_reason as transaction_reason,d.bank_name,a.bank_account,c.amount as savings 
        FROM users as a , institutions b , contributions as c , banks d where a.institution_id = b.id and a.adhersion_id = c.adhersion_id 
         and a.status='actif' and a.bank_id=d.id and c.created_at>= '2021-01-01 00:00:00' and  transaction_type ='saving' and transaction_reason='ANNUAL_INTEREST_2020';
                ";

        return DB::select($query);
    }


 /**
     * Method to generate all members with all details report
     * @return array
     */

    public function savingAllMember()
    {
         DB::statement('DROP TABLE IF EXISTS TEMP_ALLMEMBER');
         DB::statement('CREATE TEMPORARY TABLE TEMP_ALLMEMBER(
SELECT  a.adhersion_id,
        a.first_name,
        a.last_name,
        a.member_nid,
        a.institution,
        a.telephone,
        a.attorney,
        a.bank_name,
        a.bank_account,
        amount as savings,
        a.status,
        CASE 
            WHEN current_year_contribution_amount IS NULL THEN 0 
            ELSE current_year_contribution_amount 
        END AS current_year_contribution_amount,
        CASE
            WHEN current_year_contribution_count IS NULL THEN 0
            ELSE current_year_contribution_count 
        END AS current_year_contribution_count

FROM 
(SELECT a.adhersion_id,a.first_name,a.last_name,a.member_nid,b.name as institution,a.telephone,a.attorney,c.bank_name,a.bank_account,a.status FROM users as a,
institutions b ,banks c where a.institution_id = b.id and a.adhersion_id<>\'200799999\' and a.service is not null and a.bank_id=c.id) as a
    LEFT JOIN 
                (
SELECT savings.adhersion_id, 
    CASE 
       WHEN withdrawal.amount IS null THEN savings.amount
       ELSE savings.amount - withdrawal.amount END AS amount 
FROM
 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type =\'saving\' group by adhersion_id) as savings
  LEFT JOIN
 (SELECT adhersion_id, sum(amount)  as amount from  contributions where transaction_type =\'withdrawal\' group by adhersion_id) as withdrawal
 ON savings.adhersion_id = withdrawal.adhersion_id
 )
 c ON a.adhersion_id = c.adhersion_id
 LEFT JOIN (
          SELECT adhersion_id,
                 SUM(amount) current_year_contribution_amount,
                 COUNT(1) current_year_contribution_count
          FROM  contributions 
          WHERE contributions.transaction_type =\'saving\' AND 
          LEFT(contributions.created_at,4) = LEFT(CURRENT_DATE,4) and contributions.transaction_reason=\'Montly_contribution\'
          GROUP BY adhersion_id
         ) AS currentYear
  ON a.adhersion_id = currentYear.adhersion_id);'
         );
    

        $query = 'SELECT *
                  FROM  TEMP_ALLMEMBER  as a';

        $results = DB::select(DB::raw($query));
        return $results;
    }




    /**
     * Get people who didnt contribute in x amount of months
     * @param  integer $months last x months without contributions
     * @return   array
     */
     public function notContributedIn($months = 3)
    {
        $query = "
            SELECT  a.*,
            b.first_name,
            b.last_name,
            b.service,
            a.contributed_amount,
            (a.contributed_amount + (b.monthly_fee *TIMESTAMPDIFF(MONTH, last_date,CURDATE()))) as to_pay FROM 
            (
            SELECT a.adhersion_id,contributed_amount-withdrawal_amount AS contributed_amount,last_date FROM
            (SELECT 
                    adhersion_id,
                    sum(amount) as contributed_amount,
                    max(created_at) as last_date
                    FROM contributions 
                    WHERE transaction_type = 'saving'
                        GROUP BY 
                    adhersion_id
            ) AS a
            LEFT JOIN
            (
            SELECT adhersion_id, 
                    sum(amount) as withdrawal_amount 
            FROM contributions 
            WHERE transaction_type = 'withdrawal' GROUP BY adhersion_id
            )
            as b ON a.adhersion_id = b.adhersion_id
            WHERE 
            /* Smaller or equal than one month ago */
            last_date < DATE_SUB(NOW(), INTERVAL 3 MONTH)
            ) as a
            LEFT JOIN 
            users b
            ON a.adhersion_id = b.adhersion_id
            ";
        return DB::select($query);
    }

    /**
     * Get Contribution changes between dates
     * @param  startDate $startDate 
     * @param  endDate $endDate   
     * @return Collection
     */
    public static function contributionChanges($startDate,$endDate)
    {
      return DB::select('SELECT 
                            users.adhersion_id,
                            users.first_name,
                            users.last_name,
                            institutions.name institution,
                            users.service,
                            MAX(users.monthly_fee) -  MAX(contributions.amount) as balance,
                            MAX(users.monthly_fee) AS monthly_fee,
                            MAX(contributions.amount) AS amount
                        FROM users,
                        contributions,
                        institutions
                        WHERE users.monthly_fee <> contributions.amount 
                        AND contributions.transaction_type=\'saving\'
                        AND contributions.contract_number is not null
                        AND users.adhersion_id  = contributions.adhersion_id 
                        AND institutions.id = users.institution_id
                        AND contributions.created_at BETWEEN ? AND ?
                        GROUP BY 
                            users.adhersion_id,
                            users.first_name,
                            users.last_name,
                            institutions.name,
                            users.service
                        ORDER BY institutions.name,
                        (MAX(users.monthly_fee) -  MAX(contributions.amount))
                        ASC',
                        [$startDate,$endDate]);

    }
}
