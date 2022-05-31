<?php
namespace Ceb\ViewComposers;

use Illuminate\Contracts\View\View;


/**
 * AccountViewComposer
 */
class AcccountNatureViewComposer {
	
	public $accountNature = ['PASSIF' => 'Passif','ACTIF'=>'Actif','PRODUIT' => 'Produit','CHARGE'=>'Charge'];
	public function compose(View $view) {
		$view->with('accountNatures',$this->accountNature);
	}
}