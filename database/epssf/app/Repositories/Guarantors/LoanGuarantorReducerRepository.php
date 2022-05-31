<?php 

namespace Ceb\Repositories\Guarantors;

use Ceb\Models\Loan;


class LoanGuarantorReducerRepository {

	/**
	 * @var Ceb\Models\Loan
	 */
	private $loan;
	/** 
	 * Amount for cautionneur/ guarantor
	 * @var int
	 */
	private $amount;
	/** 
	 * Member of this loan
	 * @var Ceb\Models\User
	 */
	private $member;

	/**
	 * All loans with active guarantors
	 * @var collection
	 */
	private $activeLoansWithGuarantors;

	function __construct(Loan $loan,int $amount)
	{
		$this->loan                      = $loan;
		$this->amount                    = $amount;
		$this->member                    = $this->loan->member;
		$this->activeLoansWithGuarantors = $this->member->active_loans;
	}

	public function getGuarantorPercentage()
	{
		// 1. Get all active loans for this member
		// 2. Determine total loan to repay to now contribution of guarantor
		// 3. Calculate payback % per guarantor  
		// 4. Determine what amount guarantor
		// 5. should be added unto based on the %
		$totalLoansToRepay 		   = $this->loanAmountToRepay();

		$guarantorsContributions   = $this->guarantorsWithContribution($totalLoansToRepay);

	}

	/**
	 * Get loan amount to repay
	 * @param  collection $activeLoansWithGuarantors
	 * @return int | sum of loan to repay
	 */
	public function loanAmountToRepay($activeLoansWithGuarantors)
	{
		return $activeLoansWithGuarantors->sum('loan_to_repay');
	}

	/**
	 * Get guarantors with their contribution to total loans
	 * @param   int $totalLoansToRepay 
	 * @return  collection
	 */
	private function guarantorsWithContribution($totalLoansToRepay)
	{
		$guarantors = collect([]);

		foreach ($this->activeLoansWithGuarantors as $guarantor) {
			dd($guarantor);
		}

	}
}