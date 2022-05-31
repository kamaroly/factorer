<?php namespace Ceb\Repositories\Reports;

use Ceb\Models\Account;
use Ceb\Models\Attorney;
use Ceb\Models\Contribution;
use Ceb\Models\Institution;
use Ceb\Models\Journal;
use Ceb\Models\Loan;
use Ceb\Models\User;
use Ceb\Traits\FileTrait;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Support\Facades\DB;
use Str;

class GraphicReportRepository {

	public $member;
	public $account;
	public $contribution;
	public $institution;
	public $loan;			


	/**
	 * Class GraphicReportRepository
	 * @param    $member   
	 * @param    $account   
	 * @param    $contribution   
	 * @param    $loan   
	 */
	public function __construct(User $member,Account $account,Contribution $contribution,Loan $loan,Institution $institution)
	{
		$this->member = $member;
		$this->account = $account;
		$this->institution = $institution;
		$this->contribution = $contribution;
		$this->loan = $loan;
	}

	/**
	 * Get count of member per institution
	 * @return array
	 */
    public function getCountMemberPerInstitution()
    {
    	return Db::select('select a.name,count(b.adhersion_id) countStudents  
                            from institutions as a 
                            LEFT JOIN users as b 
                            ON a.id = b.institution_id group by a.name;'
                        );
    }

    /**
     * Get Institutions with loans
     * @return object 
     */
    public function getLoanByInstitution()
    {
    	return DB::select('select a.name,
                            CASE WHEN CAST(sum(b.countLoan) AS UNSIGNED) IS NULL THEN 0
                            ELSE CAST(sum(b.countLoan) AS UNSIGNED) END AS loansCount  from institutions as a LEFT JOIN
                            (
                            SELECT b.adhersion_id,
                                   b.institution_id,
                                   count(a.id) as countLoan
                                   FROM 
                                   loans as a 
                                   LEFT JOIN 
                                   users as b
                                   ON a.adhersion_id = b.adhersion_id
                             group by b.adhersion_id,
                             b.institution_id
                             )
                             as b 
                            ON a.id = b.institution_id group by a.name;'
                            );
    }

    /**
	 * Get count of member per institution
	 * @return array
	 */


     public function getMonthlyContribution()
    {
        return DB::select('select LEFT(created_at,7) as month, round(sum(amount)) as amount 
                                from `postings`
                                WHERE
                                transaction_type = ?  and 
                                LEFT(created_at,7) >= LEFT((CURRENT_DATE - INTERVAL 6 MONTH),7)
                                and account_id=9
                                group by LEFT(created_at,7)',['debit']);

    }

    public function getMontlyLoan()
    {
    	return DB::select('select CONCAT(YEAR(letter_date),MONTH(letter_date)) as month,
                            round(sum(loan_to_repay)) as amount 
                           FROM `loans` 
                            where YEAR(letter_date) >= YEAR(CURRENT_DATE - INTERVAL 12 MONTH) 
                                and 
                            MONTH(letter_date) >= MONTH(CURRENT_DATE - INTERVAL 12 MONTH) group by month');
    }



}
