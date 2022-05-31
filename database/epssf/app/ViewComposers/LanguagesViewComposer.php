<?php
namespace Ceb\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * AccountViewComposer
 */
class LanguagesViewComposer {
	public $languages = [
					'en'	=>	'English',
					'fr' 	=> 	'French',
					'kin' 	=>	'Kinyarwanda',
					];

	public function compose(View $view) {

		$view->with('languages',$this->languages);
	}
}