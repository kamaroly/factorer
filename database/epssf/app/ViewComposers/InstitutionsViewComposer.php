<?php
namespace Ceb\ViewComposers;

use Ceb\Models\Institution;
use Illuminate\Contracts\View\View;

/**
 * institutionViewComposer
 */
class InstitutionsViewComposer {
	public $institution;
	function __construct(Institution $institution) {
		$this->institution = $institution;
	}
	public function compose(View $view) {

		$institutions =  ['' => trans('institutions.select_institution')] + $this->institution->lists('name', 'id')->toArray();
		
		$view->with('institutions',$institutions);
	}
}