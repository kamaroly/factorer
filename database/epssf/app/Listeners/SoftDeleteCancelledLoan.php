<?php

namespace Ceb\Listeners;

use Carbon\Carbon;
use Ceb\Events\CorrectionStatusChanged;
use Ceb\Models\Contribution;
use Ceb\Models\Loan;
use Ceb\Models\Posting;
use Ceb\Models\Refund;
use Ceb\Traits\TransactionTrait;
use DateTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Sentry;


class SoftDeleteCancelledLoan
{
	use TransactionTrait;

    /**
     * Handle the event.
     *
     * @param  CorrectionStatusChanged  $event
     * @return void
     */
    public function handle(CorrectionStatusChanged $event)
    {
        $approval = $event->approval;
        $loan     = Loan::byTransaction($approval->transactionid)->first();
                // If Loan is not available then display this message 
        if (empty($loan)) {
            flash()->warning(trans('loan.unable_to_find_loan_with_transaction_id',['transactionID' => $approval->transactionid]));
            return false;
        }

        // proceed when a loan is found
        if ($approval->status === 'approved' && $approval->nature ==='loan') {

            // Reject this loan before recording reversed
            // Transaction
            $loan->status = 'rejected';
            $loan->save();

            $reversedLoan = $this->reverseLoan($loan,$event->approval->content);
            $this->cancelAccountingPostings($loan->postings,$reversedLoan->transactionid,$reversedLoan->comment);

            // Remove all cautionneur / guarantors
            $loan->cautions()->delete();

            flash()->success(trans('general.loan_cancelled_successfully'));
        }
    }

    /**
     * Cancel loan
     * @param   $loan 
     * @return 
     */
    private function reverseLoan($loan,string $content)
    {   
            $loan  = $loan->replicate();
            // Update wording to reflect what was written while correcting this loan
            $loan->comment                        = json_decode($content)->wording;
            $loan->transactionid                  = $this->getTransactionId(); // Generate new transaction
            $loan->right_to_loan                  = -$loan->right_to_loan;
            $loan->wished_amount                  = -$loan->wished_amount;
            $loan->loan_to_repay                  = -$loan->loan_to_repay;
            $loan->interests                      = -$loan->interests;
            $loan->InteretsPU                     = -$loan->InteretsPU;
            $loan->amount_received                = -$loan->amount_received;
            $loan->tranches_number                = -$loan->tranches_number;
            $loan->monthly_fees                   = -$loan->monthly_fees;
            $loan->remaining_tranches             = -$loan->remaining_tranches;
            $loan->special_loan_tranches          = -$loan->special_loan_tranches;
            $loan->special_loan_interests         = -$loan->special_loan_interests;
            $loan->special_loan_amount_to_receive = -$loan->special_loan_amount_to_receive;
            $loan->urgent_loan_interests          = -$loan->urgent_loan_interests;
            $loan->factor                         = -$loan->factor;
            $loan->rate                           = -$loan->rate;
            $loan->is_umergency                   = -$loan->is_umergency;
            $loan->emergency_refund               = -$loan->emergency_refund;

            // Reject this loan
            $loan->status                         = 'rejected';
            $loan->save();
            return $loan;
    }

    /**
     * Cancel postings that has happened
     * @param   $loan 
     * @return  true
     */
    private function cancelAccountingPostings($postings,$transactionId,$wording)
    {
        foreach ($postings as $posting) {

            // Do a fresh copy for replication
            $posting = $posting->replicate(['id','created_at','updated_at']);
            
            // Update transaction Id to reflect the
            // cancelled loan
            $posting->transactionid = $transactionId;
            $posting->wording       = $wording;

            switch ($posting->transaction_type) {
                case 'credit':
                    $posting->transaction_type = 'debit';
                    break;
                case 'debit':
                    $posting->transaction_type = 'credit';
                    break;
            }
            $posting->save();
        }
    }
}
