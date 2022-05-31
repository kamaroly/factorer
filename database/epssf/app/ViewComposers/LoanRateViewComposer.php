<?php
namespace Ceb\ViewComposers;

use Ceb\Models\LoanRate;
use Illuminate\Contracts\View\View;


/**
 * AccountViewComposer
 */
class LoanRateViewComposer {

	protected $loanRate;

	function __construct(LoanRate $loanRate) {
		$this->loanRate = $loanRate;
	}

	public function compose(View $view) {

		$view->with('loanRates',$this->loanRate->all());
	}
}