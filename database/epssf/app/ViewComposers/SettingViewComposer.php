<?php
namespace Ceb\ViewComposers;

use Ceb\Models\Setting;
use Illuminate\Contracts\View\View;

/**
 * member ViewComposer
 */
class SettingViewComposer {
	public $user;
	function __construct(Setting $setting) {
		$this->setting = $setting;
	}
	public function compose(View $view) {
		$view->with('setting', $this->setting);
	}
}