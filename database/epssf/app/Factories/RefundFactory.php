<?php 
namespace Ceb\Factories;

use Ceb\Models\MemberLoanCautionneur;
use Illuminate\Support\Facades\Log;
use Ceb\Traits\TransactionTrait;
use Ceb\Models\DefaultAccount;
use Ceb\Models\Institution;
use Ceb\Models\Posting;
use Ceb\Models\Refund;
use Ceb\Models\Loan;
use Ceb\Models\User;
use Sentry;
use DB;
/**
 * Refund Factory
 */
class RefundFactory {
	use TransactionTrait;
	/** 
	 * [$institution description]
	 * @var integer
	 */
	private $institution;
	function __construct(Institution $institution, Refund $refund, Posting $posting,User $member,MemberLoanCautionneur $memberLoanCautionneur) {
		$this->institution = $institution;
		$this->refund = $refund;
		$this->posting = $posting;
		$this->member = $member;
		$this->memberLoanCautionneur = $memberLoanCautionneur;
	}
	
	/**
	* Set members by institutions
	* @param integer $institutionId
	*/
	public function setByInsitution($institutionId = 1) {
		// Do we have some savings ongoing ?
		if (session()->has('refundMembers')) {
			// We have things in the session
			// Clear the session befor continuing
			$this->removeRefundMembers();
		}

		// Get the institution by its id
		$members        = $this->member->with('loans')->notLeft()
									   ->where('institution_id',(int)$institutionId)->get();
		$memberToRefund = [];
		/** Make sure we only get member with loan */
		foreach ($members as $member) {
			if (!$member->hasActiveLoan() && !$member->hasActiveEmergencyLoan) {
				flash()->warning(trans('member.this_member_doesnot_have_active_loan',
								['member' => $member->first_name.' '.$member->last_name.'-'.$member->adhersion_id]));
			    continue;
			}
	        // Make sure amount is numeric
	        $member->refund_fee = (int)   $member->loan_monthly_fee;
	        $emergencyLoan      = $member->active_emergency_loan;

	        // Active emergency Loan refund 
	        if ($this->isEmergencyLoanRefund()) { 
	        	// If this member does not have active emergency Loan 
	        	// then show a worning then continue
	        	if (empty($emergencyLoan)) {
					flash()->warning(trans('member.this_member_doesnot_have_active_emegerncy_loan',
						['member' => $member->first_name.' '.$member->last_name.'-'.$member->adhersion_id]));
					continue;
	        	}
	        	$member->refund_fee = (int) $emergencyLoan->monthly_fees;
		    }

			$memberToRefund[] = $member;
		}
		
		$this->setRefundMembers($memberToRefund);
	}
	/**
	* Set members
	* @param integer $memberId
	*
	* @return bool
	*/
	public function setMember($memberToSet = array())
	{	
		// Clean whatever member we have
		$this->removeRefundMembers();
		$members = $this->getRefundMembers();

		// Check if the provided parameter is an id for one member or not
		if (!is_array($memberToSet)) {
			// Doe we have this member 
			$member = $this->member->with('loans')->notLeft()->findOrFail($memberToSet);

			if (!$member->hasActiveLoan() && ! $member->has_active_emergency_loan ) {
				flash()->error(trans('member.this_member_doesnot_have_active_loan'));
				return false;
			}

			$member->refund_fee     = (int) $member->loan_monthly_fee;

	        // Consider emergency if we have it
	        if ($this->isEmergencyLoanRefund() && $member->has_active_emergency_loan) {
		        	$member->refund_fee = (int) $member->active_emergency_loan->monthly_fees;
	        }

			$members[] = $member;

			$this->setRefundMembers($members);
			return true;
		}

		// We have many members to upload
		if (is_array($memberToSet)) {
			$rowsWithErrors              = [];
			$rowsWithSuccess             = [];
			$rowsWithDifferentAmount     = [];
			$membersWithoutEmergencyLoan = [];
			// Go through all members one by one and see
			// Their elibility for refund loan.
			foreach ($memberToSet as $member) {
				    if (!isset($member[0]) || !isset($member[1])) {
				    	$rowsWithErrors[] = $member;
				    	continue;
				    }
               	
               	try{
					$memberFromDb           = $this->member->findByAdhersion($member[0]);
					$memberHasEmergencyLoan = $memberFromDb->has_active_emergency_loan;

					// If member does not have Emergency Loan and we are refunding Emergency
					// Then ignore this member, and add him to the list of the members
					// to ignore during this refund.
					if ($memberHasEmergencyLoan == FALSE && $this->isEmergencyLoanRefund()) {
						$membersWithoutEmergencyLoan[] = $member;
						continue;
					}

				    // If the member doesn't have active loan just skipp him
				    if (!$memberFromDb->hasActiveLoan() && !$memberHasEmergencyLoan) {
						$member[]         = '>> This member does not have an active loan!';
						$rowsWithErrors[] = $member;
						continue;
			        }

					$memberFromDb->loanMonthlyRefundFee = $memberFromDb->loan_monthly_fee;  // Monthly payment fees
					$memberFromDb->refund_fee           = (int) $member[1];
					//////////////////////////////////////////////////////////////////////
					// ADD EMERGENCY LOAN MONTHLY FEES IF THE MEMBER HAS EMERGENCY LOAN //
					//////////////////////////////////////////////////////////////////////
					if ($memberHasEmergencyLoan) {
						$emergencyLoan = $memberFromDb->activeEmergencyLoan;
			        	// If this loan was created before the time of issuing 
			        	// list to the instiution then allow to continue
			        	if ($this->shouldRefundForLoan($emergencyLoan)) {
							$memberFromDb->loanMonthlyRefundFee += $emergencyLoan->monthly_fees;
			        	}
					}

					// Does refund differ from what member entered in the file?
				    if ((int) $memberFromDb->refund_fee !== (int) $memberFromDb->loanMonthlyRefundFee) {
				    	$rowsWithDifferentAmount[] = $memberFromDb;
				    }
				    
				    $rowsWithSuccess[] = $memberFromDb;
				}
				catch(\Exception $ex){
						$member[] = $ex->getMessage().'| This member does not exist in our database';
						$rowsWithErrors[] = $member;
				}
			}	
		}

		$rowsWithErrors  		  = collect($rowsWithErrors);
		$rowsWithDifferentAmount  = collect($rowsWithDifferentAmount);
		
		if (! $rowsWithErrors->isEmpty()) {
		   $message = 'We have identified '.$rowsWithErrors->count().' member(s) with wrong format, therefore we did not consider them.';	
		}
		if (! $rowsWithDifferentAmount->isEmpty()) {
		   $message = 'We have identified '.$rowsWithDifferentAmount->count().' member(s) with  diffent contributions amount.';	
		}

		// If we have a message to show to the user
		// then display it
		if (isset($message) && !empty($message)) {
           flash()->error($message);
		}

		session()->put('membersWithoutEmergencyLoan',$membersWithoutEmergencyLoan);
		session()->put('refundsWithDifference',$rowsWithDifferentAmount);
		session()->put('uploadsWithErrors', $rowsWithErrors);
		$this->setRefundMembers($rowsWithSuccess);
		return true;
	}

	/**
	* Check if this loan sholud be refunded
	* based on the loan date
	* @param   $loan 
	* @return  boolean
	*/
	public function shouldRefundForLoan($loan)
	{
    	// We need to make sure that this emergency loan 
    	// we are about to pay for was issued after x
    	// configured days, otherwise skip it as it
    	// may  not be on the list of refunds 
    	// sent before 15th of previous month
		// Get date to compare with 
		// $startDate = date("Y-m-".env('REFUND_LOAN_AFTER_DATE','15'), strtotime('-1 month', time()));
		$loanDate  = date('Y-m',strtotime($loan->created_at));

		// If this loan was issued after date then move to 
		// the next loan
		return ($loanDate < date('Y-m'));
	}

	/**
	 * Determine if current refund is emergency
	 * @return boolean 
	 */
	public function isEmergencyLoanRefund()
	{
		return $this->getRefundLoanType() === 'EMERGENCY_LOAN';
	}

	/**
	 * Member without emergency loan
	 * @return  
	 */
	public function getRefundsWithoutEmergencyLoans()
	{
		return collect(session()->get('membersWithoutEmergencyLoan'));
	}
	/**
	* Get contributions with differences
	* 
	* @return [type] [description]
	*/
	public function getRefundsWithDifference()
	{
		return collect(session()->get('refundsWithDifference'));
	}

	/**
	* Get uploaded contribution with erros
	* @return Collection 
	*/
	public function getUploadWithErros()
	{
		return collect(session()->get('uploadsWithErrors'));
	}
	/**
	 * Remove errors from session
	 * @param  string $value 
	 * @return 
	 */
	public function forgetWithErrors($value='')
	{
		session()->forget('uploadsWithErrors');
	}

	public function  forgetMembersWithDifferences(){
		session()->forget('refundsWithDifference');
	}
	/**
	* Update a single monthly contribution for a given uses
	* @param  [type] $adhersion_number [description]
	* @param  [type] $newValue         [description]
	* @return [type]                   [description]
	*/
	public function updateRefundFee($adhersion_number, $newFee) {
		// First get what is in the session now
		$members        = $this->getRefundMembers();
		$updatedMembers = [];
		// Update members who needs to be updated
		foreach ($members as $key => $member ) {
			// If we need to  update this key then update it before returning
		  	if ($member->adhersion_id == (int) $adhersion_number) {
        		$member->refund_fee = $newFee;
        	}

        	$updatedMembers[$key] = $member;
		}
		// Now we are ready to go
		return $this->setRefundMembers($updatedMembers);
	}
	/**
	* Complete current transactions in refund
	*
	* @return  bool
	*/
	public function complete() {
		$transactionId = $this->getTransactionId(); // Generating unique transactionid
    
		// Start saving if something fails cancel everything
		DB::beginTransaction();

			$saveRefund  = $this->saveRefund($transactionId);

			$savePosting = $this->savePostings($transactionId);

			if (!$saveRefund || !$savePosting) {
				DB::rollBack();
				flash()->error(trans('refund.error_occured_during_the_processes_of_registering_refund_please_try_again'));
				return false;
			}
		// Lastly, Let's commit a transaction since we reached here
		DB::commit();
		// Remove everything from the session
		$this->clearAll();
		flash()->success(trans('refund.refun_transaction_sucessfully_registered'));
		return $transactionId;
	}
	/**
	* Saving refunds in the database
	* @param  [type] $transactionId [description]
	* @return [type]                [description]
	*/
	private function saveRefund($transactionId) {
		// Get data in the factory


		$refundMembers          = $this->getRefundMembers();
		$month                  = $this->getMonth();
		$contractNumber         = $this->getContributionContractNumber();
		$month                  = $this->getMonth();
		$emergencyLoanRefundFee = 0;
		$refundType             = $this->getRefundType();


		foreach ($refundMembers as $refundMember) {

			// Initialize emergency loan fee for each transaction to avoid
			// considering previous transaction emergecny fee

			$loan                   = Loan::find($refundMember->loan_id);
			if (empty($loan)) {
				continue;
			}
			
			$loanTransactionId      = null;
			$loanId                 = null;			
			$loanAmount             = 0;
			$emergencyMonthlyFee    = 0;
			$emergencyLoanRefundFee = 0;

			// if this member has active emergency loan, remove the normal amount to refund
			// without emergency loan and then the rest save it as emergency loan refund 
			// so that we can record how much money on emergency loan has been paid
			// back by this member

		    if ($this->getRefundLoanType() == 'EMERGENCY_LOAN') {
				    	$emergencyLoan = $loan = $refundMember->active_emergency_loan;
				    	// If member does not have emergency loan, then don't register her/him
				    	// Continue to the next one but add this to the user interface
				    	// notification and recordid this in the logs 
				    	if (empty($emergencyLoan)) {
				    		Log::warning('MEMBER DOES NOT HAVE EMERGENCY LOAN');
				    		Log::warning($emergencyLoan);
				    		flash($refundMember->first_name.' '.$refundMember->last_name."(".$refundMember->adhersion_id." ) skipped because he does not have emergency loan")->success();
				    		continue;
				    	}

    					// We have umergency loan, let's determine how much money to record as pay back
						$emergencyMonthlyFee = $emergencyLoan->monthly_fees;
						// Let's treat the case of when someone is paying back less money than 
						// what he should even pay for the emergency loan, in this case
						// we would need to consider the amount of money someone has
						// paid and ignore emergency loan monthly fees as payback
						
						if ($emergencyMonthlyFee > $refundMember->refund_fee) 
						{
							$emergencyLoanRefundFee = $refundMember->refund_fee;
						}else{
							// We  gave enough refund fees, let's do calculations
							$emergencyLoanRefundFee = $refundMember->refund_fee - $emergencyMonthlyFee;
							
							// Determine exact amount to record as emergency payback
							$emergencyLoanRefundFee = $refundMember->refund_fee - $emergencyLoanRefundFee;
						}

					 	$emergencyLoan->emergency_refund  += $emergencyLoanRefundFee;
					 	$emergencyLoan->emergency_balance -= $emergencyLoanRefundFee;

					 	// If we cannot save this operation let's fail the entire transaction
					 	if ( ! $emergencyLoan->save() ) {
					 		return false;
					 	}
						// Since we have emergency loan amount, let's save 
						// that amount with their corresponding loan id
						$loanTransactionId         = $loan->transactionid;
						$refund['adhersion_id']    = $refundMember->adhersion_id;
						$refund['contract_number'] = $loan->loan_contract;
						$refund['month']           = $this->getMonth() ?:'N/A';
						$refund['amount']          = $refundMember->refund_fee;
						$refund['tranche_number']  = $loan->tranches_number;
						$refund['transaction_id']  = $transactionId;
						$refund['member_id']       = $refundMember->id;
						$refund['user_id']         = Sentry::getUser()->id;
						$refund['loan_id']         = $loan->id;
						$refund['wording']         = $this->getRefundLoanType().'_'.$this->getWording();
				}else{
						// This is not emergency
						$loanTransactionId         = $loan->transactionid;
						$loanId                    = $loan->id;
						$refund['adhersion_id']    = $refundMember->adhersion_id;
						$refund['contract_number'] = $loan->loan_contract;
						$refund['month']           = $this->getMonth() ?:'N/A';
						$refund['amount']          = $refundMember->refund_fee;
						$refund['tranche_number']  = $loan->tranches_number;
						$refund['transaction_id']  = $transactionId;
						$refund['member_id']       = $refundMember->id;
						$refund['user_id']         = Sentry::getUser()->id;
						$refund['loan_id']         = $loan->id;
						$refund['wording']         = $this->getRefundLoanType().'_'.$this->getWording();
		    }

			$refund['refund_type']     = $refundType;

			$newRefund = $this->refund->create($refund);
			if (!$newRefund) {
				return false;
			}

			// If the loan we are paying for has cautionneur, then make sure
			// We update our member cautionneur table by adding the amount
			// paid by this member to the refund amount as long 
			// as cautionneur still have a balance
			// DISABLED AS REQUESTED BY OLIVIER IN THE FOLLOWING DOCUMENT ON POINT #4
			// https://docs.google.com/document/d/1D8Qhq4P6l8qw01Ib_6hXxdQF9XBBokf3-fhpPSlFfzs/edit?ts=5efe0598
			// if(! loanGuarantorReducer($loan,$newRefund->amount) ){
			// 	return false;
			// }	
		
			// We have recorded all refunds let's see if it comes from 
			// refund on the saving (retire par epargne ) then we 
			// need to deduct savings/contribution of this member.
			if (request()->get('refund_type') =='refund_by_epargne') {
				if (!empty($loan)) {
					$data['contract_number'] = $loan->loan_contract;
				}
				
				$data['member']         = $refundMember;
				$data['amount']         = $refundMember->refund_fee;
				$data['movement_type']  = 'withdrawal';
				$data['operation_type'] = trans('member.other_withdrawals');
				$data['wording']        = trans('refund.refund_by_epargne').'('.$this->getWording().')';
				$data['charges']        = 0;
				$contribution           = new MemberTransactionsFactory(new Institution, new Refund,new Posting,new User);
				
				return $contribution->saveContibutions($loanTransactionId,$data);
			}
		}
		return true;
	}

	/**
	* Save posting to the database
	* @param  STRING $transactionId UNIQUE TRANSACTIONID
	* @return bool
	*/
	private function savePostings($transactionId) {
		// First prepare data to use for the debit account
		// Once are have debited(deducted data) then we can
		// Credit the account to be credited
		$posting['transactionid']    = $transactionId;
		$posting['account_id']       = $this->getDebitAccount();
		$posting['journal_id']       = 1; // We assume per default we are using journal 1
		$posting['asset_type']       = null;
		$posting['amount']           = $this->getTotalRefunds();
		$posting['user_id']          = Sentry::getUser()->id;
		$posting['account_period']   = date('Y');
		$posting['transaction_type'] = 'debit';
		$posting['wording']          = $this->getWording();
		$posting['status']           = 'approved';
		// Try to post the debit before crediting another account
		$debiting = $this->posting->create($posting);
		// Change few data for crediting
		// Then try to credit the account too
		$posting['transaction_type'] = 'credit';
		$posting['account_id']       = $this->getCreditAccount();
		$crediting                   = $this->posting->create($posting);

		return ($debiting->exists && $crediting->exists);
	}
	
	/**
	* Get Total Refunds fees
	* @return decimal montly fees
	*/
	public function getTotalRefunds() {
		return collect($this->getRefundMembers())->sum('refund_fee');
	}

	/**
	* Remove one member from current contribution session
	* 
	* @param  numeric $memberId
	* @return void
	*/
	public function removeMember($adhersion_number)
	{
		// Get memebrs in current session
		$members          = $this->getRefundMembers();
		$updatedMembers   = [];
		foreach ($members as $key => $member) {
			// Skip if this member must be removed
			if ($member->adhersion_id == $adhersion_number) {
				continue;
			}
			// Update members without removed one
			$updatedMembers[$key] = $member;
		}

	   	// Update current session 
	  	$this->setRefundMembers($updatedMembers);	
	}
	/**
	* Set members who are about to refund
	* @param array $members
	*/
	public function setRefundMembers(array $members) {
		session()->put('refundMembers', collect($members));
	}
	/**
	* Get members who are refunding
	* @return array
	*/
	public function getRefundMembers() {
		return session()->get('refundMembers', []);
	}
	/**
	* Remove members who are refunding from the session
	* @return void
	*/
	public function removeRefundMembers() {
		session()->forget('refundMembers');
	}

	/**
	 * Set refund type
	 * @param null $refundType 
	 */
	public function setRefundType($refundType)
	{
		session()->put('refund_type', $refundType);
	}

	/**
	 * Get Refund type
	 * @return  string
	 */
	public function getRefundType()
	{
		return session()->get('refund_type');
	}

	/**
	 * remove Refund type
	 * @return  void
	 */
	public function removeRefundType()
	{
		session()->forget('refund_type');
	}

	/**
	 * Set refund loan type
	 * @param  $loanType 
	 */
	public function setRefundLoanType($loanType)
	{
		session()->put('refund-loan-type',$loanType);
		// If we are changing the type of loan then reload 
		// What we have in the session already if any
		$members = collect($this->getRefundMembers());
		if (! $members->isEmpty()) {
			$this->setMember($members->first()->id);
		}
	}

	/**
	 * Get refund loan type
	 * @return string 
	 */
	public function getRefundLoanType()
	{
		return session()->get('refund-loan-type');
	}

	/**
	 * Remove refund loan type from session
	 * @return void 
	 */
	public function removeRefundLoanType()
	{
		session()->forget('refund-loan-type');
	}
	/**
	* Set Month of transactions
	* @param string composed by  $month and Year
	*/
	public function setMonth($month) {
		session()->put('refundMonth', $month);
	}
	/**
	* Get the stoed month in the session
	* @return [type] [description]
	*/
	public function getMonth() {
		return session()->get('refundMonth');
	}
	/**
	* Remove refund month from the session
	* @return void
	*/
	public function removeMonth() {
		session()->forget('refundMonth');
	}
	/**
	* Set debit account ID
	* @param integer $accountid
	*/
	public function setDebitAccount($accountid) {
		session()->put('refundDebitAccount', $accountid);
	}
	/**
	* Set Credit account
	* @param  $accountid
	*/
	public function setCreditAccount($accountid) {
		session()->put('refundCreditAccount', $accountid);
	}
	/**
	* get Debit account
	* @return numeric account ID
	*/
	public function getDebitAccount() {
		$defaultDebitAccount	=  DefaultAccount::with('accounts')->debit()->refundsIndividual()->first()->accounts->first();
		// If we have many members then it's not individual refund
		// Let's change the default account
		if (count($this->getRefundMembers()) > 1) {
			$defaultDebitAccount	=  DefaultAccount::with('accounts')->debit()->RefundsBatch()->first()->accounts->first();
		}
		return session()->get('refundDebitAccount', $defaultDebitAccount->id);
	}
	/**
	* Get Credit Account
	* @return numeric unique
	*/
	public function getCreditAccount() {
		$defaultCreditAccount	=  DefaultAccount::with('accounts')->credit()->refundsIndividual()->first()->accounts->first();
		// If we have many members then it's not individual refund
		// Let's change the default account
		if (count($this->getRefundMembers()) > 1) {
			$defaultDebitAccount	=  DefaultAccount::with('accounts')->credit()->refundsBatch()->first()->accounts->first();
		}
		return session()->get('refundCreditAccount', $defaultCreditAccount->id);
	}
	/**
	* Remove debit account
	* @return void
	*/
	public function removeDebitAccount() {
		session()->forget('refundDebitAccount');
	}
	/**
	* Remove credit account from the session
	* @return void
	*/
	public function removeCreditAccount() {
		session()->forget('refundCreditAccount');
	}
	/**
	* Set the institution
	* @param mixed $institutionId
	*/
	public function setInstitution($institutionId) {
		session()->put('refundInstitution', $institutionId);
	}
	/**
	* get the current Refund institutions
	* @return ID
	*/
	public function getInstitution() {
		return session()->get('refundInstitution'); // We assume institution 1 is dhe default one
	}
	/**
	* Remove institution from the session
	* @return void
	*/
	public function removeInstitution() {
		session()->forget('refundInstitution');
	}
	/**
	* Set wording for the current contribution
	* 
	* @param void
	*/
	public function setWording($wording)
	{
		session()->put('refund_wording', $wording);
	}

	/**
	* Set the refund type in the session
	* @param $refundNature 
	*/
	public function setRefundNature($refundNature)
	{
		session()->put('refund_nature', $refundNature);
	}

	/**
	* Get refund nature
	* @param  string $value 
	* @return 
	*/
	public function getRefundNature()
	{
		return session()->get('refund_nature');
	}

	/**
	* Forge refund nature
	* @param  string $value 
	* @return   
	*/
	public function forgetRefundNature()
	{
		session()->forget('refund_nature');
	}

	/**
	* Get wording for current contributionsession
	* 
	* @return string
	*/
	public function getWording()
	{
		return session()->get('refund_wording', null);
	}

	/**
	* Forget contribution with differences
	* 		
	* @return void
	*/
	public function forgetWithDifferences()
	{
		$withDifference = collect($this->getRefundsWithDifference());
		$members        = collect($this->getRefundMembers());
		$members = $members->filter(function($member) use($withDifference){
		    return $withDifference->where('adhersion_id',$member['adhersion_id'])->isEmpty();
		});
		
		flash()->success($withDifference->count() - $members->count() . trans('refund.members_are_removed_from_this_refund_session'));
		$this->setRefundMembers($members->toArray());
		$this->forgetRefundsWithDifferences();
	}
   
    /**
	* Remove Refunds with differencdes
	* 
	* @return void
	*/
	public function forgetRefundsWithDifferences()
	{
		//1. Get the members with differences
		$allRefunds     = $this->getRefundMembers();
		$withDifference = $this->getRefundsWithDifference();

		// Remove them from others
		$rowsWithSuccess = array_filter($allRefunds,function($value) use ($withDifference) {
		
			foreach ($withDifference as $item) {
					if($item->adhersion_id == $value->adhersion_id){
						return false;
					}
			}
			
			return true;
		},ARRAY_FILTER_USE_BOTH);

		// refresh refund members
		$this->setRefundMembers($rowsWithSuccess);
		
		// then forget members with differences	
		session()->forget('refundsWithDifference');
	}

	/**
	* Remove wording from the session
	* @return [type] [description]
	*/
	public function forgetWording()
	{
		session()->forget('refund_wording');
	}
	
	/**
	* Cancel transaction that is ongoin
	* @return  void
	*/
	public function cancel() {
		$this->clearAll();
	}

	/**
	* Remove all things from the session;
	* @return
	*/
	private function clearAll() {
		$this->removeRefundMembers();
		$this->removeMonth();
		$this->removeInstitution();
		$this->removeDebitAccount();
		$this->removeCreditAccount();
		$this->forgetWording();
		$this->removeRefundType();
		$this->forgetWithErrors();
		$this->forgetMembersWithDifferences();
		$this->forgetRefundNature();
	}
}