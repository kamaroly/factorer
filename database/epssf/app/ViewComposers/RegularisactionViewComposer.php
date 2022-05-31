<?php
namespace Ceb\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * AccountViewComposer
 */
class RegularisactionViewComposer {

	private $regularisationTypes = [];

	function __construct() {
		$this->setRegularisationTypes();
	}


	public function compose(View $view) {
		$view->with('regularisations',$this->regularisationTypes);
	}

    /**
     * Gets the value of regularisationTypes.
     *
     * @return mixed
     */
    public function getRegularisationTypes()
    {
        return $this->regularisationTypes;
    }

    /**
     * Sets the value of regularisationTypes.
     *
     * @return mixed
     */
    public function setRegularisationTypes()
    {

    $this->regularisationTypes = [
								'installments'					=> trans('navigations.installments'),
								'amount'						=> trans('navigations.amount'),
								'amount_installments'			=> trans('navigations.amount_installments'),
								];
    }
}