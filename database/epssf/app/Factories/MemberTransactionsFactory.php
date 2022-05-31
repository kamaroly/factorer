<?php
namespace Ceb\Factories;

use Ceb\Models\Contribution;
use Ceb\Models\Institution;
use Ceb\Models\Loan;
use Ceb\Models\Posting;
use Ceb\Models\Refund;
use Ceb\Models\User;
use Ceb\Traits\TransactionTrait;
use DB;
use Illuminate\Support\Facades\Session;
use Sentry;

/**
 * Refund Factory
 */
class MemberTransactionsFactory {
    use TransactionTrait;

    /** Variable to  hold the object that are going to be injected */
    private $institution;

    function __construct(Institution $institution, Refund $refund, Posting $posting,User $member) {
        $this->institution = $institution;
        $this->refund = $refund;
        $this->posting = $posting;
        $this->member = $member;
    }

    /**
     * Complete current transactions in contribution
     *
     * @return  bool
     */
    public function complete(array $data) {

        $transactionId = $this->getTransactionId();
       
        // Start saving if something fails cancel everything
        Db::beginTransaction();
        $saveContibution = $this->saveContibutions($transactionId,$data);
        $savePosting = $this->savePostings($transactionId,$data);
        
        // Rollback the transaction via if one of the insert fails
        if (!$saveContibution || !$savePosting) {
            DB::rollBack();
            return false;
        }

        // Lastly, Let's commit a transaction since we reached here
        DB::commit();
        return $transactionId;

    }

    /**
     * Saving contribution as per contribution factory
     *
     * @return bool
     */
    public function saveContibutions($transactionId,$data = array()) {

            if (count($data) == 0 ) { // Provided data is empty we have nothing to do here
                flash()->error(trans('member.member_transaction_data_not_provided'));
                return false;
            }
            $charges = 0;
            // if (isset($data['charges'])) {
            //     $charges = ($data['amount'] * (int) $data['charges']) / 100;
            // }
           
            $contribution['transactionid']        = $transactionId;
            $contribution['month']                = date('m');
            $contribution['institution_id']        = $data['member']->institution_id;
            $contribution['amount']                = $data['amount'];
            $contribution['state']                = 'Ancien';
            $contribution['year']                = date('Y');
            $contribution['contract_number']    = $data['contract_number'];
            $contribution['transaction_type']    = $data['movement_type'];
            $contribution['transaction_reason']    = $data['operation_type'];
            $contribution['wording']            = $data['wording'];
            $contribution['adhersion_id']        = $data['member']->adhersion_id;
            $contribution['charged_amount']        = $charges;
            //$contribution['charged_percentage']    = $data['charges'];
           
            //Remove unwanted column
            unset($contribution['id']);

            # try to save if it doesn't work then
            # exist the loop
            $newContribution = Contribution::create($contribution);

            if (!$newContribution) {
                return false;
            }

            /**
             * @todo if this transaction is for relicat, then ADD NEW LOAN/CREDIT EPARGNE
             */
            if ($contribution['transaction_reason']=='remainers' && $contribution['transaction_type'] =='saving') {
                // Add contract number here ....
                if (!$this->recordLoan($contribution)) {
                        return false;
                }
            }
        return true;
    }

    /**
     * Record loadn
     * @param   $input 
     * @return    
     */
    public function recordLoan($input)
    {
        // Prepare information to be saved in the database
        $data['transactionid']                  = trim($input['transactionid']);
        $data['loan_contract']                  = trim($input['contract_number']);
        $data['adhersion_id']                   = $input['adhersion_id'];
        $data['movement_nature']                = $input['transaction_reason'];
        $data['operation_type']                 = 'loan_relicat';
        $data['letter_date']                    = Date('Y-m-d');
        $data['right_to_loan']                  = 0;
        $data['wished_amount']                  = $input['amount'];
        $data['loan_to_repay']                  = $input['amount'];
        $data['interests']                      = 0;
        $data['InteretsPU']                     = 0;
        $data['amount_received']                = $input['amount'];
        $data['tranches_number']                = 0;
        $data['cheque_number']                  = '';
        $data['bank_id']                        = '';
        $data['security_type']                  = 0;
        $data['cautionneur1']                   = 0;
        $data['cautionneur2']                   = 0;
        $data['average_refund']                 = 0;
        $data['amount_refounded']               = 0;
        $data['comment']                        = $input['wording'];
        $data['special_loan_contract_number']   = 0;
        $data['remaining_tranches']             = 1;
        $data['monthly_fees']                   = 0;
        $data['special_loan_tranches']          = 0;
        $data['special_loan_interests']         = 0;
        $data['special_loan_amount_to_receive'] = 0;
        $data['rate']                           = 0;
        $data['status']                         = 'approved';
        $data['reason']                         = 'Transaction relicat';
        $data['urgent_loan_interests']          = 0;
        $data['user_id']                        = Sentry::getUser()->id;

        return Loan::create($data);
    }

    /**
     * Save posting to the database
     * @param  STRING $transactionId UNIQUE TRANSACTIONID
     * @return bool
     */
    private function savePostings($transactionId,$data = array()) {

        if (count($data) == 0) { // Provided data is empty we have nothing to do here
            flash()->error(trans('member.member_transaction_data_not_provided'));
            return false;
        }
        
         $wording = $data['wording'];

        //Debiting....
        $debits = $this->joinAccountWithAmount($data['debit_accounts'], $data['debit_amounts']);

        //Crediting
        $credits = $this->joinAccountWithAmount($data['credit_accounts'], $data['credit_amounts']);

        // Let's again, make sure that accounting records are valid
        if (!$this->isValidPosting($debits,$credits)) {
            return false;
        }
       
        // We are good to go, attempt to save
        // debiting accounts and amount
       
        foreach ($debits as $accountId => $amount) {
            $results = $this->savePosting($accountId, $amount, $transactionId, 'debit', $journalId = 1,$wording,$cheque_number=null,$bank=null,$status='approved');
            if (!$results) {

                return false;
            }
        }

        // Debiting amount has been well saved,
        // Let's attempt to save credit account
       
        foreach ($credits as $accountId => $amount) {
            $results = $this->savePosting($accountId, $amount, $transactionId, 'credit', $journalId = 1,$wording,$cheque_number=null,$bank=null,$status='approved');
            if (!$results) {
                return false;
            }
        }

        // We are safe here
        return true;
    }

   
    /**
     * Get transaction type
     * @param  numeric $movement_type_id
     * @return string                 
     */
    public function getTransactionType($movement_type_id)
    {
        switch ($movement_type_id) {
            case 1:
                $transactionType = 'saving';
                break;
            case 2:
                $transactionType = 'withdrawal';
                break;
            default:
                $transactionType = 'Unknow';
                break;
        }
        return $transactionType;
    }

}