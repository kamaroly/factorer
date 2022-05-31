<?php
namespace Ceb\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * AccountViewComposer
 */
class LoanStatusViewComposer {
	public function compose(View $view) {
		$loanStatuses = 
					[
					'all'       => trans('general.all'),
					'unblocked' => trans('loan.unblocked'),
					'pending' 	=> trans('loan.pending'),
					'approved'	=> trans('loan.approved'),
					'rejected' 	=> trans('loan.rejected'),
					];

		$view->with('loanStatuses',$loanStatuses);
	}
}