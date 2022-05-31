<?php
namespace Ceb\ViewComposers;

use Ceb\Repositories\Account\AccountRepository;
use Illuminate\Contracts\View\View;

/**
 * AccountViewComposer
 */
class AccountViewComposer {
	public $account;
	function __construct(AccountRepository $account) {
		$this->account = $account;
	}
	public function compose(View $view) {
		$view->with('accounts', $this->account->dropDownList());
	}
}