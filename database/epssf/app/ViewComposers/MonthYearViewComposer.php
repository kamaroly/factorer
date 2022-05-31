<?php
namespace Ceb\ViewComposers;
use Illuminate\Contracts\View\View;

/**
 * Months Year list
 */
class MonthYearViewComposer {
	/**
	 * Get months Year
	 * @return array contains months between -6 and +6
	 */
	public function getMonthYear() {
		$monthYear = [];

		for ($i = -6; $i < 6; $i++) {
			$monthYear[date('Ym', strtotime(" $i months"))] = date('Ym', strtotime(" $i months"));
		}
		return $monthYear;
	}

	public function compose(View $view) {
		$view->with('monthYear', $this->getMonthYear());
	}

}