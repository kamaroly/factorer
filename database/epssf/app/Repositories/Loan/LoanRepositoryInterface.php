<?php 
namespace Ceb\Repositories\Loan;

interface LoanRepositoryInterface{
	/**
	 * Get sum of the ordinary loan
	 * @return numeric 
	 */
	public function getOrdinaryLoanSum();

	/**
	 * Get sum of the special loan
	 * @return numeric 
	 */
	public function getSpecialLoanSum();

	/**
	 * Get sum of the social_loan loan
	 * @return numeric 
	*/
	public function getSocialLoanSum();

	/**
	 * Get sum of the urgent_ordinary_loan loan
	 * @return numeric 
	*/
	public function getUrgentOrdinaryLoanSum();

}