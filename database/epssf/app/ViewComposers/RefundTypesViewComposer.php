<?php
namespace Ceb\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * AccountViewComposer
 */
class RefundTypesViewComposer {
	protected $refundTypes; 

	function __construct( ) {
		$this->setRefundTypes();
	}
	public function compose(View $view) {
		$view->with('refundTypes',$this->refundTypes);
	}

	public function setRefundTypes()
	{
	 $this->refundTypes= [
					'refund_by_banque'				=> trans('refund.refund_by_banque'),
					'refund_by_salaire'				=> trans('refund.refund_by_salaire'),
					'refund_by_epargne'				=> trans('refund.refund_by_epargne'),
					'refund_by_cautionneur'			=> trans('refund.refund_by_cautionneur'),
					'refund_by_decompte_final'		=> trans('refund.refund_by_decompte_final'),
					'refund_by_relicats'			=> trans('refund.refund_by_relicats'),
					'refund_by_interets_retournes'	=> trans('refund.refund_by_interets_retournes'),
					'refund_by_interets_annuels'	=> trans('refund.refund_by_interets_annuels'),
					];
	}
}