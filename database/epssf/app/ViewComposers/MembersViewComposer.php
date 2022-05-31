<?php
namespace Ceb\ViewComposers;

use Ceb\Models\User;
use Illuminate\Contracts\View\View;

/**
 * member ViewComposer
 */
class MembersViewComposer {
	public $member;
	function __construct(User $member) {
		$this->member = $member;
	}
	public function compose(View $view) {
		$view->with('membersCount', $this->member->count());
	}
}