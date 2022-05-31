<?php
namespace Ceb\Factories;

use DB;
use Log;
use Sentry;
use Datetime;
use Validator;
use Exception;
use Ceb\Models\Loan;
use Ceb\Models\User;
use Ceb\Models\Posting;
use Ceb\Models\Setting;
use Ceb\Models\LoanRate;
use Ceb\Models\UserGroup;
use Ceb\Traits\TransactionTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Ceb\Models\MemberLoanCautionneur;
use Illuminate\Support\Facades\Session;
use Fenos\Notifynder\Facades\Notifynder;

/**
 * This factory that deals with loan
 */
class LoanFactory {
	use TransactionTrait;
	protected $errors;
	protected $wishedAmountPercentage;
	function __construct(Session $session, User $member, Loan $loan,LoanRate $loanRate, Posting $posting,Setting $setting) {
		$this->session = $session;
		$this->member = $member;
		$this->loan = $loan;
		$this->loanRate = $loanRate;
		$this->posting = $posting;
		$this->setting = $setting;	
		$this->user    = Sentry::getUser();
		$this->wishedAmountPercentage = $this->setting->keyValue('loan.wished.amount');
	}
	/**
	 * Add member to is going to receive loan
	 * @param integer $mId ID of the member to add to the session
	 */
	function addMember($mId) {
	// Only check member ceb age when it's not 
	$member = $this->member;
	$opType = $this->getOperationType();
	$member = (strtolower($opType) ==  'emergency_loan') ? $member->find($mId) : $member->eligible($mId)->find($mId);
		
	// Detect if this member is not more than 6 months
	// as per de definition of CEB
	if (is_null($member)) {
		flash()->error(trans('loan.error_member_has_to_be_at_least_6_months_in_ceb'));
		return false;
	}

	// We don't allow someone to take a loan while there is another loan still waiting.
	$hasBlockedLoan   = Loan::where('adhersion_id',$member->adhersion_id)->blocked()->first();
	$hasUnBlockedLoan = Loan::where('adhersion_id',$member->adhersion_id)->unBlocked()->first();
	if (!empty($hasBlockedLoan) || !empty($hasUnBlockedLoan)) {
		flash()->error(trans('loan.sorry_this_member_has_a_loan_in_process_his_first_loan_has_to_be_approved_or_rejected_before_continuing'));
		return false;
	}


	// We don't allow someone with active emergency loan to take another 
	// Loan
	if (!empty($member->active_emergency_loan) && strtolower($opType) ==  'emergency_loan') {
		flash()->error(trans('loan.error_sorry_but_you_have_an_existing_emergency_loan'));
		return false;
	}


	// Does this member has a loan ?
	// if yes inspect the loan details
	$latestLoan = $member->latestLoanWithEmergency();
	if (!is_null($latestLoan)) 
	{
    
    // If latest loan this member took is emergency 
    // there is no need to validate it like other
    // types of loan just skip it as request by 
    // Olivier on 11 March 2016 at 18:02
	if ($latestLoan->is_umergency != 1) {
	
		/** If we are allowed to provide loan to people with negative right to loan */
	 	if ($this->setting->keyValue('loan.allow.member.with.negative.right.to.loan') == 0) {
	 		if ($member->right_to_loan < 1) {
				flash()->error(trans('loan.this_member_doesnot_have_enough_right_to_loan_because_he_has_pending_loans'));
				$this->cancel();
				return false;
			}
	 	}
	 	// Check if in the settings we have allowed people to hav more than 1 ordinary loan
	 	// and the current loan is not an emergency loan
	 	if ($this->setting->keyValue('loan.allow.one.ordinary.loan.only') == 1) {
		 		if ($member->has_active_loan) {
		 			flash()->warning(trans('loan.this_member_has_active_ordinary_loan'));
		 			// Change operation type to default if the member still has ordinary 
		 			// loan to pay back
		 			if (strpos($this->getLoanInput('operation_type'), 'ordinary_loan') !== false) {
						$this->addLoanInput(['operation_type'=>'special_loan']);
		 			}
		 		}
		 	}
		  }
		 }
		$this->setMember($member);

		$this->updateCautionneur();
	}

	/**
	 * Set member in the session
	 * @param User $member 
	 */
	public function setMember(User $member)
	{
		return session()->set('loan_member',$member);
	}

	/**
	 * Get member who is going to receive the loan
	 * @return  get member information that are currently in the session
	 */
	function getMember() {
		return session()->get('loan_member', new $this->member);
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
		session()->put('loanInputs', $loanInputsData);
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
		session()->put('loanInputs', $loanInputsData);
	}
	/**
	 * Set amount bonded for this loan
	 * @param  $amountBonded 
	 */		
	public function setBondedAmount()
	{		
		session()->put('bonded_amount', request('amount_bonded'));
	}
	/**
	 * Get bonded amount value
	 * @return  numeric
	 */
	public function getBondedAmount()
	{
		return session()->get('bonded_amount', 0);
	}
	/**
	 * Forget bonded amount
	 * @return 
	 */
	public function forgetBondedAmount()
	{
		session()->forget('bonded_amount');
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
		return session()->get('loanInputs', []);
	}
	/**
	 * Set new cautionneur
	 * @param array $cautionneur
	 */
	public function setCautionneur(array $cautionneur) 
	{
		$arrayKey       = array_keys($cautionneur)[0];
		$cautionneurId  = array_values($cautionneur)[0];
		$cautionneurs   = $this->getCautionneurs();

		// Get rid of any empty array element we may have
		$cautionneurs   = is_array($cautionneurs) ? array_filter($cautionneurs):[];
		$newCautionneur = $this->member->findByAdhersion($cautionneurId);
		$loanMember     = $this->getMember();
       
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
		if ($newCautionneur->id == $loanMember->id) {
			flash()->error(trans('loan.cautionneur_should_not_be_the_same_as_the_member_requesting_loan'));
			return false;
		}

		/**
		 * CEB UPGRADE 2020, CONDITIONS ADDED AS PER OLIVIER DEMANDED IN BELOW DOCUMENT.
		 * https://docs.google.com/document/d/1D8Qhq4P6l8qw01Ib_6hXxdQF9XBBokf3-fhpPSlFfzs/edit?usp=sharing
		 */
		// a) For Emergency loan if  savings  are less than loan balance (ordinary and other) we  have to add guarantors
		// b) One member  can not guarantor  more  than  3 members 
		if (! $newCautionneur->canCaution($loanMember->adhersion_id)) {
			// c) During guarantor  adding system should  check  if  that member  selected  have not  guaranteed more than 3 members, 
			//    If yes  a message should  appear  showing that the  member  selected not allowed  because  have exceeded  3  members
			flash()->error(trans('loan.cautionneur_maximized_allowed_number_of_caution_please_select_another_person_as_guarantor'));
			return false;
		}
		

	    $cautionneurs[$arrayKey] = $newCautionneur;
		// Add this to loan input
		$this->addLoanInput([$arrayKey=>$newCautionneur->id]);
		session()->put('cautionneurs', $cautionneurs);
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
		flash()->success(trans('loan.cautionneur_removed_successfully'));
		session()->put('cautionneurs', $cautionneurs);
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
		session()->put('cautionneurs', $cautionneurs);
	}
	/**
	 * Get cautionneurs set
	 * @return  array of cautionneur
	 */
	public function getCautionneurs() {
		return session()->get('cautionneurs',[]);
	}
    
	/**
	 * Setting the debit accounts and their value
	 *
	 * @param void
	 */
	public function setDebitsAccounts(array $debits, array $amounts) {
		session()->put('debitaccounts', $this->accountAmount($debits, $amounts));
	}
	/**
	 * Get the debit accounts
	 * @return array of debit account and their amount
	 */
	public function getDebitAccounts() {
		return session()->get('debitaccounts', []);
	}
	/**
	 * Setting Credit accounts in the sessions
	 * @param array $credits contains the IDs of the accounts to be credited
	 * @param array $amounts contains the amount of money to be credited per account
	 *
	 * @return void
	 */
	public function setCreditAccounts(array $credits, array $amounts) {
		session()->put('creditaccounts', $this->accountAmount($credits, $amounts));
	}
	/**
	 * Get the credit accounts and their correspondent amount
	 * @return array of the credit account and their correspondent amount
	 */
	public function getCreditAccounts() {
		return session()->get('creditaccounts', []);
	}
	/**
	 * Remove credit accounts from the sessoin
	 * @return void
	 */
	public function removeCreditAccounts() {
		session()->forget('creditaccounts');
	}
	/**
	 * Remote debit accounts and their amount from the session
	 * @return void
	 */
	public function removeDebitAccounts() {
		session()->forget('debitaccounts');
	}
	public function setOperationType($operation_type)
	{
		return session()->set('operation_type', $operation_type);
	}
	/**
	 * Get the operation type of this loan
	 * @return string the operation type of this loan
	 */
	public function getOperationType() {

		if (isset($this->getLoanInputs()['operation_type'])) {
			return $this->getLoanInputs()['operation_type'];		
		}
		
		return 'ordinary_loan';
	}
	/**
	 * Complete current loan transaction
	 * @return bool
	 */
	public function complete() {
	try {
		// 1. First record the loan
		$transactionId = $this->getTransactionId();
		$this->setBondedAmount();
		$this->calculateLoanDetails();

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
                   ->send();
           }
		}
        
        /** Notify the requestor */
        Notifynder::category('loan.request.received')
                   ->from($this->user->id)
                   ->to($this->getMember()->id)
                   ->url(route('loan.pending',['loanid'=>$saveLoan->id]))
                   ->send();
       
       $user = $this->getMember();
    
	   $data['names'] = $user->names;
	   $inputs = $this->getLoanInputs();
	   $data['amount'] = $inputs['loan_to_repay'];
	   Mail::queue('emails.newloan', $data, function ($message) use ($user) {
		    $message->to($user->email);
		    $message->subject("New loan");
	   });

		// Since we are done let's make sure everything is cleaned fo
		// the next transaction
		$this->clearAll();
		// Lastly, Let's commit a transaction since we reached here
		DB::commit();

		return $transactionId;
			
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e);
			return false;
		}
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
			 flash()->error(trans('loan.loan_information_seem_not_to_be_correct').' because '.$this->errors);
			return false;
		}
		// If we reach here it means data are valid, therefore let's try
		// to continue processing the saving activity
		$inputs = $this->getLoanInputs();
		$member = $this->getMember();
		// Prepare information to be saved in the database
		$data['transactionid']                  = $transactionid;
		$data['loan_contract']                  = $this->getContributionContractNumber();
		$data['adhersion_id']                   = $member->adhersion_id;
		$data['movement_nature']                =isset( $inputs['movement_nature'])? $inputs['movement_nature']:'saving';
		$data['operation_type']                 = $this->getOperationType();
		$data['letter_date']                    = $this->getLetterDate() .' '. date('H:i:s');
		$data['right_to_loan']                  = $inputs['right_to_loan'];
		$data['wished_amount']                  = $inputs['wished_amount'];
		$data['loan_to_repay']                  = $inputs['loan_to_repay'];
		$data['interests']                      = $inputs['interests'];
		$data['InteretsPU']                     = 0;
		$data['amount_received']                = $inputs['amount_received'];
		$data['tranches_number']                = $this->getTranschesNumber();
		$data['cheque_number']                  = isset($inputs['cheque_number']) ? $inputs['cheque_number'] : '';
		$data['bank_id']                        = isset($inputs['bank_id']) ? $inputs['bank_id'] : '';
		$data['security_type']                  = 0;
		$data['cautionneur1']                   = isset($inputs['cautionneur1']) ? $inputs['cautionneur1'] : 0;
		$data['cautionneur2']                   = isset($inputs['cautionneur2']) ? $inputs['cautionneur2'] : 0;
		$data['average_refund']                 = 0;
		$data['amount_refounded']               = 0;
		$data['comment']                        = $inputs['wording'];
		$data['special_loan_contract_number']   = 0;
		$data['remaining_tranches']             = isset($inputs['tranches_number']) ? $inputs['tranches_number'] : 1;
		$data['monthly_fees']                   = $inputs['monthly_fees'];
		$data['special_loan_tranches']          = 0;
		$data['special_loan_interests']         = 0;
		$data['special_loan_amount_to_receive'] = 0;
		$data['rate']                           = $this->getInterestRate();
		$data['reason']                         = isset($inputs['reason']) ? $inputs['reason'] : null;
		$data['urgent_loan_interests']          = $inputs['urgent_loan_interests'];
		$data['user_id']                        = Sentry::getUser()->id;
		// If operation type is emergency loan then add emergency loan details 
		if (strtolower($data['operation_type']) ==  'emergency_loan') {
			$data['interests'] 		 = $data['loan_to_repay'] * ($this->getInterestRate() /100);
			$data['monthly_fees']    = $data['loan_to_repay'] / $data['tranches_number'];
			$data['is_umergency']    = 1;
			$data['emergency_balance'] = $data['loan_to_repay'];
		}
		
		// If Loan is social loan or special loan use latest ordinary loan contract number
		if (strtolower(trim($data['operation_type'])) == 'special_loan' || strtolower(trim($data['operation_type'])) == 'social_loan' ) {
			try
			{
				$data['loan_contract'] = $member->Latest_ordinary_loan->loan_contract;
			}
			catch(\Exception $ex)
			{
				Log::error($ex->getMessage());
			}
		}
        $newLoan = $this->loan->create($data);

        $amountToRepay = $inputs['loan_to_repay'];

        // If this member has existing loan then consider current 
        // Loan balance while calculating the amount to bond
        if (isset($inputs['previous_loan_balance'])) {
        	$amountToRepay = $amountToRepay + $inputs['previous_loan_balance']; 
        }

        // Notify customer that we are not recording this caution because member 
        // has enough balance, he does not need cautionneurs.
	    // If we have bond then save cautionneurs also in the database
		if ($this->getBondedAmount() > 0 && ($amountToRepay > $inputs['contributions'])){
			// Attempt to record the loan
			if ($this->recordCautionneurs($transactionid, $newLoan->id) == false) {
				return false;
			}
		}
		// if we reach here it means that we didn't have bond therefore let's continue
		return $newLoan;
	}
	/**
	 * Record cautionneur details for this loan
	 * @param  string $transactionid 
	 * @param  string $loanId        
	 * @return bool         
	 */
	public function recordCautionneurs($transactionid,$loanId)
	{
		$cautionneurs = $this->getCautionneurs();
		$member 	  = $this->getMember();

		// Devide amount equally 
		$amount  = $this->getBondedAmount() / count($cautionneurs);
		foreach ($cautionneurs as $cautionneur) {
				$memberLoanCautionneur = new MemberLoanCautionneur;
				$memberLoanCautionneur->member_adhersion_id       = $member->adhersion_id;
				$memberLoanCautionneur->cautionneur_adhresion_id  = $cautionneur->adhersion_id;
				$memberLoanCautionneur->amount                    = $amount;
				$memberLoanCautionneur->transaction_id			  = $transactionid;
				$memberLoanCautionneur->loan_id                   = $loanId;
				$memberLoanCautionneur->letter_date			  	  = $this->getLoanInput('letter_date');
				// Fail transaction if something went wrong
				if(!$memberLoanCautionneur->save())
				{
					return false;
				}
		}
		return true;
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
	public function getInterestRate() {
		/** Adding exception for the emergency loan */
		if ($this->getOperationType() == 'emergency_loan') {
			return $this->setting->keyValue('loan.emergency.rate');
		}
		$numberOfInstallment = $this->getTranschesNumber();
		return $this->loanRate->rate($numberOfInstallment,$numberOfInstallment);
	}
	/**
	 * Calculate loan details
	 * @return mixed
	 */
	public function calculateLoanDetails($validation = false) {
		$loanDetails = $this->getLoanInputs();
      	
		$loanToRepay         = isset($loanDetails['loan_to_repay'])?$loanDetails['loan_to_repay']:0;
		$wishedAmount        = isset($loanDetails['wished_amount']) ?  $loanDetails['wished_amount'] : 
		round(($loanToRepay * $this->wishedAmountPercentage), 0);
		$interestRate        = $this->getInterestRate();
		$administration_fees = (int) $this->setting->keyValue('loan.administration.fee');
		$numberOfInstallment = $this->getTranschesNumber();
		$totalLoanToRepay    = 0;
		if (isset($loanDetails['operation_type'])==true) {
 		 if (!isset($loanDetails['previous_loan_balance'])) {
	   			$loanDetails['previous_loan_balance'] = 0;
	   		}
	       $totalLoanToRepay= $loanToRepay+(int) $loanDetails['previous_loan_balance'];
		}
		else
		{
			$loanDetails['operation_type'] = null;
		}
	
		// Interest formular
		// The formular to calculate interests at ceb is as following
		// I =  P *(TI * N)
		//     ------------
		//     1200 + (TI*N)
		//
		// Where :   I : Interest
		//           P : Amount to Repay
		//           TI: Interest Rate
		//           N : Montly payment
		// LoanToRepay * (InterestRate*NumberOfInstallment) / 1200 +(InterestRate*NumberOfInstallment)
        

		///$interests = calculateInterest($loanToRepay,$interestRate,$numberOfInstallment);


		//dd($interests);

		if($numberOfInstallment<=12)
				{
						//dd($numberOfInstallment);
					$interests = ($loanToRepay*$numberOfInstallment)*2/1200;

                }
           else

           	{
           			///dd($numberOfInstallment);
					$interests = ($loanToRepay*$numberOfInstallment)*4/2400;
                }

		if ($this->getLoanInput('operation_type')=='special_loan') {
			$interests   = 0;
 			$netToReceive = $loanToRepay - $interests;
         }

		$netToReceive = $loanToRepay - $interests;
		
		if($numberOfInstallment == 0){
			$numberOfInstallment = 1;
		}


		// Update fields
		$this->addLoanInput(['wished_amount' => $wishedAmount]);
		$this->addLoanInput(['interests' => round($interests, 0)]);
		$this->addLoanInput(['net_to_receive' => round($netToReceive, 0)]);
	    $this->addLoanInput(['urgent_loan_interests' => 0]);
		$this->addLoanInput(['monthly_fees' => round(($totalLoanToRepay / $numberOfInstallment), 0)]);
		$this->addLoanInput(['adhersion_id' => $this->getMember()->adhersion_id]);
		$this->addLoanInput(['rate' => $interestRate]);
		
		// If this loan is urgent loan, then calculate administration fees of 5% on loan to repay
		// And remove it from the net_to_receive
		if (strtolower($loanDetails['operation_type']) == 'urgent_ordinary_loan') {
		 $urgent_loan_interests = round($loanToRepay, 0) * ($administration_fees / 100);
		 $this->addLoanInput(['urgent_loan_interests' => $urgent_loan_interests]);
		 $this->addLoanInput(['net_to_receive' =>  round($netToReceive, 0) - $urgent_loan_interests]);
		}
		// Add cautionneur
		foreach ($this->getCautionneurs() as $key => $value) {
			$this->addLoanInput([$key => $value->id]);
		}
        
		return true;
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
		session()->forget('loan_member');
	}
	/**
	 * Clear all things in the session that are related to the loan
	 */
	public function clearAll() {
		$this->clearMember();
		$this->forgetBondedAmount();
		session()->forget('loanInputs');
		session()->forget('cautionneurs');
		session()->forget('creditaccounts');
		session()->forget('debitaccounts');
		session()->forget('completionStatus');
	}
}
