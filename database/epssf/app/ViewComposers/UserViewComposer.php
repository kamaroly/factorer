<?php
namespace Ceb\ViewComposers;

use Ceb\Models\User;
use Illuminate\Contracts\View\View;

/**
 * member ViewComposer
 */
class UserViewComposer {
	public $user;
	function __construct(User $user) {
		$this->CebUser = $user;
	}
	public function compose(View $view) {
		$view->with('CebUser', $this->CebUser);
	}
}