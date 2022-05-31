<?php 
namespace Ceb\Repositories\Loan;

use Ceb\Models\Loan;
use Ceb\Models\Refund;
/**
* Loan repository Interface
*/
class LoanRepository
{
	/**
	 * Store loan model
	 * @var Ceb\Models\Loan
	 */
	public $loan;

	function __construct()
	{
		$this->loan 	= (new Loan);
		$this->refund 	= (new  Refund);
	}

	/**
	 * Get sum of the ordinary loan
	 * @return numeric 
	 */
	public function getOrdinaryLoanSum(){
		return $this->loan->where('operation_type','ordinary_loan')->sum('loan_to_repay');
	}

	/**
	 * Get sum of the special loan
	 * @return numeric 
	 */
	public function getSpecialLoanSum(){
		return $this->loan->where('operation_type','special_loan')->sum('loan_to_repay');
	}
    
    /**
	 * Get sum of the social_loan loan
	 * @return numeric 
	 */
	public function getSocialLoanSum(){
		return $this->loan->where('operation_type','social_loan')->sum('loan_to_repay');
	}
	/**
	 * Get sum of the urgent_ordinary_loan loan
	 * @return numeric 
	 */
	public function getUrgentOrdinaryLoanSum(){
		return $this->loan->where('operation_type','urgent_ordinary_loan')->sum('loan_to_repay');
	}

	/**
	 * Get sum of outstanding amount
	 *
	 * @return numeric
	 */
	public function getOutStandingAmount()
	{
		$loans   = $this->loan->sum('loan_to_repay');
		$refunds = $this->refund->sum('amount');
		return $loans - $refunds; 
	}


	/**
	 * Determine if member has fully Paid this loan 
	 * this is different from the isFullPaid()
	 * because this one check per member
	 * 
	 * @return boolean 
	 */
	public function FullPaid()
	{
		return 'count';
	}
}