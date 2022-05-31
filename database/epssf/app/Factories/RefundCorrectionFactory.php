<?php 
namespace Ceb\Factories;

use Ceb\Events\RefundAdjusted;
use Ceb\Events\RefundDeleted;
use Ceb\Models\Posting;
use Ceb\Models\Refund;
/**
 * Refund Correction factory
 */
class RefundCorrectionFactory{

	/**
	 * Remove transaction from database
	 * @return boolean
	 */
	public function removeTransaction()
	{
            $transactionid  = $this->getRefunds()->first()['transactionid'];

            $refunds        = Refund::byTransaction($transactionid);
            $deletedRefunds = $refunds->get();

            if ($refunds->delete()) {

                // Remove all postings related to the deleted contribution
                if (! $deletedRefunds->isEmpty()) {
                    Posting::byTransaction($deletedRefunds->first()->transactionid)->delete();
                }

                event(new RefundDeleted($deletedRefunds));
                flash()->success(trans('general.refund_removed_from_our_record_and_cannot_be_restored'));

                return $this->clear();
            }
            return false;
	}
	/**
	 * Set refunds in the current sessions
	 * @param  $refunds 
	 */
	public function setRefunds($refunds)
	{
		session()->put('refunds',collect($refunds));
	}

	/**
	 * Get Refunds for corrections
	 * @return  collection
	 */
	public function getRefunds()
	{
		return session()->get('refunds',collect([]));
	}

	/**
	 * Remove all items in the session
	 * @return void
	 */
	public function clear()
	{
		session()->forget('refunds');
	}
}