<?php
namespace Ceb\Factories;
use Exception;
use Ceb\Models\Loan;
use Ceb\Models\LoanRate;
use Ceb\Models\MemberLoanCautionneur;
use Ceb\Models\Posting;
use Ceb\Models\Setting;
use Ceb\Models\User;
use Ceb\Models\UserGroup;
use Ceb\Traits\TransactionTrait;
use DB;
use Datetime;
use Fenos\Notifynder\Facades\Notifynder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Sentry;
use Validator;

/**
 * This factory helps Contribution
 */
class RegularisationFactory {
	use TransactionTrait;
	protected $errors;
	protected $wishedAmountPercentage;

	function __construct(Session $session, 
						User $member, 
						Loan $loan,
						LoanRate $loanRate, 
						Posting $posting,
						Setting $setting) {
		
		$this->session  = $session;
		$this->member   = $member;
		$this->loan     = $loan;
		$this->loanRate = $loanRate;
		$this->posting  = $posting;
		$this->setting  = $setting;	
		$this->user     = Sentry::getUser();
		$this->wishedAmountPercentage = $this->setting->keyValue('loan.wished.amount');
	}

	/**
	 * Add member to is going to receive loan
	 * @param integer $memberId ID of the member to add to the session
	 */
	function addMember($memberId) {
		$member = $this->member->eligible($memberId)->find($memberId);
      	
		// Detect if this member is not more than 6 months
		// as per de definition of CEB
		if (is_null($member)) {
			flash()->error(trans('loan.error_member_has_to_be_at_least_6_months_in_ceb'));
			return false;
		}

		// Throw exception if right to loan is not positive
		// if ($member->right_to_loan <= 0 ) {
		// 	throw new Exception("Selected Member has 0 right to loan, Please select another person", 1);
		// }

      	// Determine which operation type so that we can know what the 
      	// member is eligible for
      	$operationType = $this->getLoanInput('operation_type');

      	$elibilityType = $member->loan_to_regulate;

		/** THIS MEMBER DOES NOT HAVE ANYTHING TO REGULATE */
		if ($elibilityType == -1) {
			flash()->error(trans('loan.does_not_have_active_right_to_regulate',['names'=>$member->names]));
			return false;
		}
		Session::put('regulate_loan_member', $member);

		$this->updateCautionneur();
	}

	/**
	 * Get member who is going to receive the loan
	 * @return  get member information that are currently in the session
	 */
	function getMember() {
		return Session::get('regulate_loan_member', new $this->member);
	}

	/**
	 * Add loan input in the session
	 * @param  array  $data containing loan input data
	 * @return void
	 */
	public function addLoanInput(array $data) {

		// First get the existing loan inputs
		$loanInputsData = $this->getLoanInputs();
        
		// Add the submitted one before saving
		foreach ($data as $key => $value) {
			$loanInputsData[$key] = $value;
		}

		if (isset($loanInputsData['debit_accounts']) && $loanInputsData['credit_accounts']) {
			$this->setDebitsAccounts($loanInputsData['debit_accounts'], $loanInputsData['debit_amounts']);
			$this->setCreditAccounts($loanInputsData['credit_accounts'], $loanInputsData['credit_amounts']);
		}
		Session::put('regulate_loanInputs', $loanInputsData);
	}

	/**
	 * Remove one item in the loan input
	 */
	public function removeLoanInput($arrayKey)
	{
		// First get the existing loan inputs
		$loanInputsData = $this->getLoanInputs();

		// Add the submitted one before saving
		foreach ($loanInputsData as $key => $value) {
			if ($key == $arrayKey) {
				unset($loanInputsData[$key]);
				break;
			}
		}
		Session::put('regulate_loanInputs', $loanInputsData);
	}

	/**
	 * Set amount bonded for this loan
	 * @param  $amountBonded 
	 */		
	public function setBondedAmount()
	{
		Session::put('regulate_bonded_amount',request()->get('amount_bonded'));
	}

	/**
	 * Get bonded amount value
	 * @return  numeric
	 */
	public function getBondedAmount()
	{
		return Session::get('regulate_bonded_amount', 0);
	}

	/**
	 * Forget bonded amount
	 * @return 
	 */
	public function forgetBondedAmount()
	{
		Session::forget('regulate_bonded_amount');
	}

	/**
	 * Get loan input by key
	 * @param  string $key 
	 * @return    
	 */
	public function getLoanInput($key)
	{
		$loanInputs = $this->getLoanInputs();
		return isset($loanInputs[$key]) ? $loanInputs[$key] : null; 
	}
	/**
	 * Get current loan input fields
	 * @return array
	 */
	public function getLoanInputs() {
		return Session::get('regulate_loanInputs', []);
	}
	/**
	 * Set new cautionneur
	 * @param array $cautionneur
	 */
	public function setCautionneur(array $cautionneur) 
	{
		$arrayKey = array_keys($cautionneur)[0];
		$cautionneurId = array_values($cautionneur)[0];
		$cautionneurs = $this->getCautionneurs();

		// Get rid of any empty array element we may have
        $cautionneurs = is_array($cautionneurs) ? array_filter($cautionneurs):[];

        $newCautionneur = $this->member->findByAdhersion($cautionneurId);

        if ($newCautionneur == null) {
			flash()->error(trans('loan.adhersion_number_you_are_looking_for_cannot_be_found'));
			// Nothing $to do here
			return false;
		}


		if (!empty($cautionneurs) && is_array($cautionneurs)) {
		$existingKey  = array_keys($cautionneurs)[0];
        // Make sure we are not setting one cautionneurs two times
    	if ($cautionneurs[$existingKey]->id == $newCautionneur->id) {
    		flash()->error(trans('loan.the_member_you_are_trying_to_set_is_already_set_as_cautionneur_please_choose_another_member'));
				// Nothing to do here
			return false;
	        }
		}

		// Make sure the selected cautionneur is not
		// same as the member
		if ($newCautionneur->id == $this->getMember()->id) {
			flash()->error(trans('loan.cautionneur_should_not_be_the_same_as_the_member_requesting_loan'));
			return false;
		}
	
	    $cautionneurs[$arrayKey]= $newCautionneur;

		// Add this to loan input
		$this->addLoanInput([$arrayKey=>$newCautionneur->id]);
		Session::put('regulate_cautionneurs', $cautionneurs);

		return true;
	}

	/**
	 * Remove cautionneur from the sessoin
	 * @param  string $cautionneur
	 * @return mixed
	 */
	public function removeCautionneur($cautionneur) {
		$cautionneurs = $this->getCautionneurs();
		// Remove the cautionneur
		unset($cautionneurs[$cautionneur]);
        $this->removeLoanInput($cautionneur);
		flash()->success(trans('loan.cautionneur_removed_successfully'));
		Session::put('regulate_cautionneurs', $cautionneurs);
	}

	/**
	 * Make sure cautionneur are not same as the
	 * Selected member
	 *
	 */
	public function updateCautionneur() {
		$cautionneurs = $this->getCautionneurs();
		foreach ($cautionneurs as $key => $cautionneur) {
			if ($cautionneur->id == $this->getMember()->id) {
				unset($cautionneurs[$key]);
			}
		}
		Session::put('regulate_cautionneurs', $cautionneurs);
	}

	/**
	 * Get cautionneurs set
	 * @return  array of cautionneur
	 */
	public function getCautionneurs() {
		return Session::get('regulate_cautionneurs',[]);
	}
    

	/**
	 * Setting the debit accounts and their value
	 *
	 * @param void
	 */
	public function setDebitsAccounts(array $debits, array $amounts) {
		Session::put('regulate_debitaccounts', $this->accountAmount($debits, $amounts));
	}

	/**
	 * Get the debit accounts
	 * @return array of debit account and their amount
	 */
	public function getDebitAccounts() {
		return Session::get('regulate_debitaccounts', []);
	}

	/**
	 * Setting Credit accounts in the sessions
	 * @param array $credits contains the IDs of the accounts to be credited
	 * @param array $amounts contains the amount of money to be credited per account
	 *
	 * @return void
	 */
	public function setCreditAccounts(array $credits, array $amounts) {
		Session::put('regulate_creditaccounts', $this->accountAmount($credits, $amounts));
	}

	/**
	 * Get the credit accounts and their correspondent amount
	 * @return array of the credit account and their correspondent amount
	 */
	public function getCreditAccounts() {
		return Session::get('regulate_creditaccounts', []);
	}

	/**
	 * Remove credit accounts from the sessoin
	 * @return void
	 */
	public function removeCreditAccounts() {
		Session::forget('regulate_creditaccounts');
	}
	/**
	 * Remote debit accounts and their amount from the session
	 * @return void
	 */
	public function removeDebitAccounts() {
		Session::forget('regulate_debitaccounts');
	}

	public function setOperationType($operation_type)
	{
		return Session::set('regulate_operation_type', $operation_type);
	}
	/**
	 * Get the operation type of this loan
	 * @return string the operation type of this loan
	 */
	public function getOperationType() {
		return isset($this->getLoanInputs()['operation_type'])?$this->getLoanInputs()['operation_type']:'installments';
	}

	/**
	 * Complete current loan transaction
	 * @return bool
	 */
	public function complete() {

		// 1. First record the loan
		$transactionId = $this->getTransactionId();
		$this->calculateLoanDetails();
		$this->setBondedAmount();

		// Start saving if something fails cancel everything
		Db::beginTransaction();

		// 1. Save loans first
		$saveLoan = $this->saveLoan($transactionId);

		// 2. Debit and credit accounts
		$savePosting = $this->savePostings($transactionId);


		// Rollback the transaction via if one of the insert fails
		if (!$saveLoan || !$savePosting) {
			DB::rollBack();
			return false;
		}

		// Lastly, Let's commit a transaction since we reached here
		DB::commit();
        
        // Notify all people who has right to approve loan 
        // Get all users who have the right to approve leave
        // if we found them then ilitirate them and 
        // make sure, we notify all of them
        $groups = UserGroup::with('users')->get();

        foreach ($groups as $group) {      
            
            // If this group doesn't have access then 
            // go to the next group
            
            if (!$group->hasAccess('loan.can.unblock.loan')) {
                continue;
            }

            // Group has access let's notify them
           foreach ($group->users as $user) {
               Notifynder::category('loan.approval')
                   ->from($this->user->id)
                   ->to($user->id)
                   ->url(route('loan.blocked',['loanid'=>$saveLoan->id]))
                   ->sendWithEmail();
           }
		}
        
        /** Notify the requestor */
        Notifynder::category('loan.request.received')
                   ->from($this->user->id)
                   ->to($this->getMember()->id)
                   ->url(route('loan.pending',['loanid'=>$saveLoan->id]))
                   ->sendWithEmail();
        
		// Since we are done let's make sure everything is cleaned fo
		// the next transaction
		$this->clearAll();
		return $transactionId;

	}

	/**
	 * Generate contract 
	 * @param  loanId $value 
	 * @return view   
	 */
	public function makeContract($loan)
	{
		// Refresh the member information
		$member = $this->member->find($this->getMember()->id);
	    $loan = $this->loan->find($loan->id);	
		$operation_type = $this->getOperationType();

		$loan->contract = generateContract($member,strtolower($operation_type));

		$loan->save();		
		return $loan->id;
	}

	/**
	 * Save loan in postings for the accounting purpose
	 * @param  string $transactionid unique transactionId
	 * @return bool
	 */
	public function saveLoan($transactionid) {

		// First refresh the data and validate them
		// if the data are not validated we will recieve false
		if (!$this->calculateLoanDetails(true)) {
			// We have nothing to do here, First return false with
			// Error that says information provided is not correct
			 flash()->error(trans('loan.regularisation_information_seem_not_to_be_correct').' because '.$this->errors);

			return false;
		}
		// If we reach here it means data are valid, therefore let's try
		// to continue processing the saving activity
		$inputs       = $this->getLoanInputs();
		
		$member       = $this->getMember();
		$cautionneurs = $this->getCautionneurs();

		$loan   = $member->loan_to_regulate;

		// Prepare information to be saved in the database by refereing to previous ordinary loan
		if ($loan == -1) {
			flash()->error(trans('loan.this_member_does_not_have_loan_to_regulate'));
			return false;
		}

		$loan = $member->latest_ordinary_loan;
		$loanToRegulate = $loan->replicate(); # Clone the loan into a new, non-existing instance.
		
		// If this loan is from existing regulated, then 
		// take last number and increment it.
		$loanContract = $loanToRegulate->loan_contract; //.'200'; for distinguish with other loans
		
		if ($loanToRegulate->is_regulation) {
			$loanContract = $loanToRegulate->loan_contract+1;
		}
      
		$inputs['amount_received']       = isset($inputs['amount_received']) ?$inputs['amount_received']:$inputs['netToReceive'];
		$loanToRegulate->transactionid         = $transactionid;
		$loanToRegulate->loan_contract         = $loanContract;
		$loanToRegulate->right_to_loan         = (int) ($inputs['right_to_loan'] - $inputs['additional_amount']);
		$loanToRegulate->wished_amount         = round($inputs['additional_amount'],0);
		$loanToRegulate->loan_to_repay         = round($inputs['additional_amount'],0);
		$loanToRegulate->interests             = round($inputs['total_interests'], 0);
		$loanToRegulate->amount_received       = round($inputs['amount_received'],0);
		$loanToRegulate->monthly_fees          = round($inputs['new_monthly_fees'],0);
		$loanToRegulate->tranches_number       = isset($inputs['new_installments']) ? $inputs['new_installments'] : 0;
		$loanToRegulate->cheque_number         = '';
		$loanToRegulate->bank_id               = '';
		$loanToRegulate->cautionneur1          = isset($cautionneurs['cautionneur1']) ? $cautionneurs['cautionneur1']->adhersion_id : '';
		$loanToRegulate->cautionneur2          = isset($cautionneurs['cautionneur2']) ? $cautionneurs['cautionneur2']->adhersion_id : '';;
		$loanToRegulate->contract              = '';
		$loanToRegulate->operation_type        = 'regulation';
		$loanToRegulate->comment               = $inputs['wording'];
		$loanToRegulate->status                = 'pending';
		$loanToRegulate->security_type         = isset($inputs['movement_nature']) ? $inputs['movement_nature'] : 'saving';
		$loanToRegulate->urgent_loan_interests = round($inputs['additinal_charges']);
		$loanToRegulate->user_id               = Sentry::getUser()->id;
		$loanToRegulate->is_regulation         = true;
		$loanToRegulate->letter_date           = date("Y-m-d h:i:s");
		$loanToRegulate->regulation_type       = $inputs['operation_type'];

        if ($loanToRegulate->save() == false) {
        	return false;
        }

	    // If we have bond then save cautionneurs also  in the database
		if (isset($cautionneurs['cautionneur1']) && $this->getBondedAmount() > 0) {
			// Attempt to record the loan
			if (! $this->recordCautionneurs($transactionid, $loanToRegulate->id)) {
				return false;
			}
		}
		// if we reach here it means that we didn't have bond therefore let's continue
		return $loanToRegulate;
	}

	/**
	 * Record cautionneur details for this loan
	 * @param  string $transactionid 
	 * @param  string $loanId        
	 * @return bool         
	 */
	public function recordCautionneurs($transactionid,$loanId)
	{
		$cautionneurs      = $this->getCautionneurs();
		$member            = $this->getMember();
		$successfulResults = FALSE;

		foreach ($cautionneurs as $cautionneur) {
				$memberLoanCautionneur                           = new MemberLoanCautionneur;
				$memberLoanCautionneur->member_adhersion_id      = $member->adhersion_id;
				$memberLoanCautionneur->cautionneur_adhresion_id = $cautionneur->adhersion_id;
				$memberLoanCautionneur->amount                   = $this->getBondedAmount() / count($cautionneurs);
				$memberLoanCautionneur->transaction_id           = $transactionid;
				$memberLoanCautionneur->loan_id                  = $loanId;
				$memberLoanCautionneur->letter_date              = $this->getLoanInput('letter_date');

				// Fail transaction if something went wrong
				$successfulResults = $memberLoanCautionneur->save();
		}
		return $successfulResults;
	}


	/**
	 * Save posting to the databasee
	 * @param  STRING $transactionId UNIQUE TRANSACTIONID
	 * @return bool
	 */
	private function savePostings($transactionId) {

		// Start by validating the information we
		// are about to svae in our database
		if (!$this->isValidPosting()) {
			return false;
		}

	    $inputs = $this->getLoanInputs();
		//Debiting....
		$debits = $this->getDebitAccounts();

		foreach ($debits as $accountId => $amount) {
			$results = $this->savePosting($accountId, $amount, $transactionId, 'debit', $journalId = 1,$inputs['wording'],$cheque_number=null,$bank=null,$status='pending');
			if (!$results) {
				return false;
			}
		}

		//Crediting
		$credits = $this->getCreditAccounts();
		foreach ($credits as $accountId => $amount) {
			$results = $this->savePosting($accountId, $amount, $transactionId, 'credit', $journalId = 1,$inputs['wording'],$cheque_number=null,$bank=null,$status='pending');
			if (!$results) {
				return false;
			}
		}
		// We are safe here
		return true;
	}

	/**
	 * Get number of installment to pay
	 * @return [type] [description]
	 */
	private function getTranschesNumber() {
		$loanInputs = $this->getLoanInputs();
		return $numberOfInstallment = isset($loanInputs['tranches_number']) ? $loanInputs['tranches_number'] : 1;

	}

	/**
	 * Get the letter date
	 * @return date if it's not available we assume today was the letter day
	 */
	private function getLetterDate() {
		return isset($inputs['letter_date']) ? $inputs['letter_date'] : date('Y-m-d');
	}

	/**
	 * Get loan interest
	 * @return float
	 */
	public function getInterestRate($numberOfInstallment) {
		return $this->loanRate->rate($numberOfInstallment,$numberOfInstallment);
	}

	/**
	 * Calculate loan details
	 * @return mixed
	 */
	public function calculateLoanDetails() {
		$loanDetails = $this->getLoanInputs();

	
		$loanDetails['additional_amount']			=$additional_amount				= isset($loanDetails['additional_amount']) ? (int) $loanDetails['additional_amount'] : 0;				
		$loanDetails['loanBalance']					=$loanBalance					= isset($loanDetails['previous_loan_balance']) ?(int) $loanDetails['previous_loan_balance'] : 0;
		$loanDetails['additional_installments']		=$additional_installments		= isset($loanDetails['additional_installments']) ? (int) $loanDetails['additional_installments'] : 0;
		$loanDetails['remaining_installments']		=$remaining_installments		= isset($loanDetails['current_number_of_installments']) ? (int) $loanDetails['current_number_of_installments']:0;				
		$loanDetails['totalContributions']			=$totalContributions			= 0;				
		$loanDetails['additinal_charges_rate']		=$additinal_charges_rate		= isset($loanDetails['charges_rate']) ? (int) $loanDetails['charges_rate'] : 0;				
		$loanDetails['additinal_charges']			=$additinal_charges				= 0;				
		$loanDetails['remaining_interest']			=$remaining_interest			= 0;				
		$loanDetails['totalInstallement_interests']	=$totalInstallement_interests	= 0;
		$loanDetails['interest_on_installements']	=$interest_on_installements		= 0;				
		$loanDetails['interest_on_amount']			=$interest_on_amount			= 0;
	    $interests_to_pay							= 0;				 
		$loanDetails['total_interests']				=$total_interests				= 0;				
		$loanDetails['new_monthly_fees']			=$new_monthly_fees				= 0;				
		$loanDetails['netToReceive']				=$netToReceive					= 0;	
		$loanDetails['numberOfInstallment']			=$numberOfInstallment 			= $remaining_installments + $additional_installments;

		// Calculate remaining interests
		$interestRate		= (float) $this->getInterestRate($remaining_installments);
		$remaining_interest	= (int) calculateInterest($loanBalance,$interestRate,$remaining_installments);
		
      	if (strtolower($loanDetails['operation_type']) == 'installments') {
      	     // THIS IS A INSTALLMENT REGULATION 
				$interestRate		= (float) $this->getInterestRate($numberOfInstallment);
				$loanDetails['totalInstallement_interests']	= calculateInterest($loanBalance,$interestRate,$numberOfInstallment);
				$loanDetails['interest_on_installements']	= $loanDetails['totalInstallement_interests'] - $remaining_interest;
				$loanDetails['new_monthly_fees']			= $loanBalance / $numberOfInstallment;
				$loanDetails['total_interests']				= (int) $loanDetails['interests_to_pay'];
      		} 

      	else if (strtolower($loanDetails['operation_type']) == 'amount') {
      	   // THIS IS A AMOUNT REGULATION 
				$loanDetails['totalInstallement_interests']	= calculateInterest(($loanBalance + $additional_amount),$interestRate,$remaining_installments);
				$loanDetails['interest_on_amount']			= round($loanDetails['totalInstallement_interests'] - $remaining_interest,0);
				
				if ($numberOfInstallment == 0) {
					$numberOfInstallment = 1;
				}

				$loanDetails['new_monthly_fees']			= $loanBalance / $numberOfInstallment;
				// If we have additiona charges such as administration fees
				// Then charge them here
				if ($additinal_charges_rate > 0) 
				{
					$loanDetails['additinal_charges']    = ($additional_amount * $additinal_charges_rate)/  100;
				};

				$loanDetails['total_interests']		= $total_interests	= $loanDetails['interest_on_amount'];
				$loanDetails['netToReceive']		= $netToReceive		= $additional_amount - $interest_on_installements - $interest_on_amount- $additinal_charges;
				$loanDetails['new_monthly_fees']	= $new_monthly_fees	= ($loanBalance + $additional_amount )/$numberOfInstallment;
		}
		else if (strtolower($loanDetails['operation_type']) == 'amount_installments')
		{    // THIS IS A AMOUNT AND INSTALLMENT REGULATION 
				$interestRate		= (float) $this->getInterestRate($numberOfInstallment);
				$loanDetails['totalInstallement_interests']	= $totalInstallement_interests	= calculateInterest($loanBalance,$interestRate,$numberOfInstallment);
				$loanDetails['interest_on_installements']	= $interest_on_installements	= $loanDetails['totalInstallement_interests'] - $remaining_interest;
				$loanDetails['totalInstallement_interests']	= $totalInstallement_interests	= calculateInterest($loanBalance + $additional_amount,$interestRate,$numberOfInstallment);
				$loanDetails['interest_on_amount']			= $interest_on_amount			= $loanDetails['totalInstallement_interests'] - $remaining_interest;

				// If we have additiona charges such as administration fees
				// Then charge them here
				if ($additinal_charges_rate > 0) {
					$loanDetails['additinal_charges'] = $additinal_charges	= ($additional_amount * $additinal_charges_rate)/  100;
				};

				//This  change was  to  fix  issue  of  interest combined  instellement and  amount
				$loanDetails['total_interests']		= $loanDetails['interests_to_pay'];// $total_interests		= $interest_on_amount + $interest_on_installements;
				$loanDetails['netToReceive']		= $netToReceive		= $additional_amount - $interest_on_installements - $interest_on_amount- $additinal_charges;
				$loanDetails['new_monthly_fees']	= $new_monthly_fees	= ($loanBalance + $additional_amount )/$numberOfInstallment;
		}
      	else
      	{
      		return false;
      	}

	    $this->addLoanInput($loanDetails);
	    return $loanDetails;	
	}

	/**
	 * Cancel all activity by clearing session
	 * @return void
	 */
	public function cancel() {
		$this->clearAll();
	}

	/**
	 * Check if this person we are trying to give
	 * Loan is eligeable for it
	 * @param  User    $user member Object
	 * @return boolean    determine if he is eligeable or not
	 */
	private function isEligeable(User $user) {
		// Check if the member has an age
		// of 6 months in CEB
		$dateDifference = date_diff($user->created_at, new Datetime);
		// Get the difference in months
		$interval = $dateDifference->format('%m');

		// Check if the months are at least 6 configured to 1 for the
		// Development purpose
		return $interval >= $this->setting->keyValue('loan.member.minimum.months');
	}

	/**
	 * Validate if the accounts and amount per accounts
	 * are valid before saving them in the database
	 * @return boolean
	 */
	private function isValidPosting() {

		$debits = $this->getDebitAccounts();
		$credits = $this->getCreditAccounts();

		// First check if the sum of both credits
		// and Debits has same sum of amount so that
		// we can make sure that this double entry is balanced
		if (array_sum($credits) != array_sum($debits)) {
			// just exit because we have nothing to do here...
			flash()->error(trans('loan.debits_and_credits_amount_must_be_equal'));
			return false;
		}

		// Let's check if the user is trying to debit and credit
		// Same account, which is not allowed as per account laws

		if (empty(count(array_diff_key($debits, $credits)))) {
			flash()->error(trans('loan.it_is_not_allowed_to_credit_and_debit_same_account_please_correct_and_try_again'));
			return false;
		}
		// Ahwii ! time to read the bible, let's exit here wi
		// Good news
		return true;
	}
	/**
	 * Remove member from the session
	 * @return [type]
	 */
	public function clearMember() {
		Session::forget('regulate_loan_member');
	}
	/**
	 * Clear all things in the session that are related to the loan
	 */
	private function clearAll() {
		$this->clearMember();
		$this->forgetBondedAmount();
		Session::forget('regulate_loanInputs');
		Session::forget('regulate_cautionneurs');
		Session::forget('regulate_creditaccounts');
		Session::forget('regulate_debitaccounts');
		Session::forget('regulate_completionStatus');
	}
}
