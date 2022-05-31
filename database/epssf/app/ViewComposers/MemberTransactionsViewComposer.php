<?php
namespace Ceb\ViewComposers;

use Ceb\Models\User;
use Illuminate\Contracts\View\View;

/**
 * member ViewComposer
 */
class MemberTransactionsViewComposer {

	private $transactions;

	function __construct()
	{
		$this->setTransactions();
	}

 	/**
	 * Possible member transactions
	 * @var array
	 */
	public function setTransactions()
	{
   $this->transactions = 
			['saving' =>[
							'individual_monthly_contribution'			=>trans('member.individual_monthly_contribution'),
							'annual_interest'							=>trans('member.annual_interest'),
							'returned_interest'							=>trans('member.returned_interest'),
							'remainers'									=>trans('member.remainers'),
							'repayments_of_third_party_debt_payment'	=>trans('member.repayments_of_third_party_debt_payment'),
							'regularization'							=>trans('member.regularization'),
							'other_savings'								=>trans('member.other_savings'),
							],

			 'withdrawal'=>[
							'withdrawal_a_third'			=>trans('member.withdrawal_a_third'),
							'full_withdrawal_savings'		=>trans('member.full_withdrawal_savings'),
							'withdrawal_social_portion'		=>trans('member.withdrawal_social_portion'),
							'release_social_portion'		=>trans('member.release_social_portion'),
							'annual_interest_withdrawal'	=>trans('member.annual_interest_withdrawal'),
							'withdrawal_returned_interests'	=>trans('member.withdrawal_returned_interests'),
							'withdrawal_remaining_balances'	=>trans('member.withdrawal_remaining_balances'),
							'regularization'				=>trans('member.regularization'),
							'other_withdrawals'				=>trans('member.other_withdrawals'),
			 			],
			];
		}

	   public function compose(View $view) {
		$transactionTypes = [];
		foreach ($this->transactions as $key => $value) {
			$transactionTypes[$key] = trans('member.'.$key);
		}
		

		$view->with(['memberTransactions'=> $this->transactions,'transactionTypes'=>$transactionTypes]);
	}

}