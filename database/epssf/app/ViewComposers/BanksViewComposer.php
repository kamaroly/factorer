<?php
namespace Ceb\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * AccountViewComposer
 */
class BanksViewComposer {

	public function compose(View $view) {

		$view->with('banks',config('ceb.banks', [
						'BK' =>	'Bank of Kigali',
						'BCR'=> 'Commercial Bank of RWanda',
						'BPR'=>	'Bank Populaire du Rwanda',
					]));
	}
}