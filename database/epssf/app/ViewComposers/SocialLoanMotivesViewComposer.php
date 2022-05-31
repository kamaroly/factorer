<?php
namespace Ceb\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * AccountViewComposer
 */
class SocialLoanMotivesViewComposer {
	public function compose(View $view) {
		$reasons = 
					[
					0           => trans('general.select_reason'),
					'sickness'	=>	trans('general.sickness'),
					'accident' 	=> 	trans('general.accident'),
					'death' 	=>	trans('general.death'),
					'others'	=>  trans('general.other_reasons'),
					];

		$view->with('socialLoanReasons',$reasons);
	}
}