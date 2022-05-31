<?php
namespace Ceb\Http\Controllers;

use Ceb\Factories\LoanFactory;
use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests\CompleteLoanRequest;
use Ceb\Http\Requests\UnblockLoanRequest;
use Ceb\Models\DefaultAccount;
use Ceb\Models\Loan;
use Ceb\Models\MemberLoanCautionneur;
use Ceb\Models\User as Member;
use Ceb\Models\User;
use Ceb\Models\UserGroup;
use Ceb\ViewComposers\LoanTypeViewComposer;
use Fenos\Notifynder\Facades\Notifynder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Input;
use Redirect;
use Session;

class LoanController extends Controller {
	protected $loanId = 0;
	protected $currentMember = null;
	protected $loanFactory;
	protected $member;
	protected $loan;

	function __construct(LoanFactory $loanFactory,Member $member,Loan $loan) {
	
		$this->member      = $member;
		$this->loanFactory = $loanFactory;
		$this->loan        = $loan;
		parent::__construct();
	}
	/**
	 * Display a listing of the resource.
	 * @param integer $loanId determines if the loan is completed or
	 * @return Response
	 */
	public function index() {
	// First check if the user has the permission to do this
        if (!$this->user->hasAccess('loan.index')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        $inputs = Input::all();
        // First log 
        Log::info($this->user->email . ' is viewing loan index',$inputs);
		
		// If we have anything in parameters to set, just set it
		if (count($inputs) > 0) {
			foreach ($inputs as $key => $value) {
				$this->loanFactory->addLoanInput([$key =>$value]);
			}
		}
		
		return $this->reload();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function selectMember($id) {
	   // First check if the user has the permission to do this
        if (!$this->user->hasAccess('loan.add.member.to.loan.form')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is adding member to loan form');
		
		// Add the select member to the session
		$this->loanFactory->addMember($id);
		return $this->reload();
	}

	/**
	 * Complete loan transaction
	 * @return mixed
	 */
	public function complete(CompleteLoanRequest $request) {

		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('loan.complete.loan.request')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is completing loan request');
	
		$memberId = $this->loanFactory->getMember()->id;

		// Make sure we update with latest form inputs
		$this->loanFactory->addLoanInput($request->all());

        // Update accounting fields too
        $this->ajaxAccountingFeilds();
     
        // Only complete transaction when someone does a post request
        if ($request->isMethod('post')) {
	        // Complete transaction
			if ($loanId = $this->loanFactory->complete()) {
				$message             = trans('loan.loan_completed');
				$this->loanId        = $loanId;
				$this->currentMember = $memberId;

				flash()->success($message);
				$this->loanFactory->clearAll();
				// If this user doesn't have right to view the contract
				// Then show him an error
		        if (!$this->user->hasAccess('reports.contract.loan')) {
		            flash()->warning(trans('loan.loan_completed_but_we_didnot_show_you_contract_because_you_do_not_have_the_right_to_view_it'));
		            $this->loanId = 0;
		        }
			}
        }
       
		return $this->reload($this->loanId);
	}
	/**
	 * Cancel ongoing loan transaction
	 * @return mixed
	 */
	public function cancel() {
		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('loan.cancel.loan.request')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is cancelling loan request');
	
		$this->loanFactory->cancel();
		$message = trans('loan.loan_cancelled');
		flash()->success($message);

		return Redirect::route('loans.index');
	}

	/**
	 * Set new cautionneur
	 */
	public function setCautionneur() {
		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('loan.set.loan.cautionneur')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // Member cannot be cautionneur to him self
        if (in_array($this->loanFactory->getMember()->adhersion_id,request()->only('cautionneur1','cautionneur2'))) {
            flash()->warning(trans('general.member_cannot_be_guarantor_to_himself'));
        	return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is setting loan cautionneur');
	
		if($this->loanFactory->setCautionneur(Input::all())){			
			flash()->success(trans('loan.cautionneur_has_been_added_successfully'));
		}

		$loanid = Session::get('loan_id', null);
		return $this->reload($loanid); 
	}
	/**
	 * Remove cautionneur from the loan Factory
	 * @param  string $cautionneur
	 * @return   mixed
	 */
	public function removeCautionneur($cautionneur) {
	    // First check if the user has the permission to do this
        if (!$this->user->hasAccess('loan.remove.loan.cautionneur')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is setting loan cautionneur');
	
		$this->loanFactory->removeCautionneur($cautionneur);

		return $this->reload();
	}

	/**
	 * Reload loan page
	 * @return mixed
	 */
	private function reload($loanId = 0) {
		
		$member     = $this->loanFactory->getMember();
		$loanInputs = $this->loanFactory->getLoanInputs();
		$loanTypes  = (new LoanTypeViewComposer)->getLoanTypes();

		$loanInputs['operation_type'] = $this->getOperationType($loanInputs,$member);

		if ($member->has_active_loan) {
			// Also remove the ordinary loan
			// If member has emergency loan remove as well.
			unset($loanTypes['ordinary_loan']);
			unset($loanTypes['urgent_ordinary_loan']);
		}

		$creditAccounts  = $this->loanFactory->getCreditAccounts();
		$debitAccounts   = $this->loanFactory->getDebitAccounts();
		$cautionneurs    = $this->loanFactory->getCautionneurs();
		$operationType   = $this->loanFactory->getOperationType();
		$currentMemberId = $this->currentMember;
		$activeLoan      = $this->loan;
		$rightToLoan     = 0;

		if ($member->exists) {
			$rightToLoan = $member->right_to_loan;
		}

		$operation_type = strtolower($loanInputs['operation_type']);
		/**
		 * Get what the user is allowed to have if configured 
		 */
		$settingKey = $operation_type.'.amount';

		if ($this->setting->hasKey($settingKey) !== false) {
			 $rightToLoan = $this->setting->keyValue($settingKey);
			 $activeLoan = $member->latestLoan();
		}

        /** DETERMINE WHICH DEFAULT ACCOUNTS TO SET */
        $defaultAccounts = $this->getDefaultAccounts($operation_type);
		return view('loansandrepayments.index', 
			        compact('member',
			        	    'loanId',
			        	    'defaultAccounts',
			        	    'rightToLoan',
			        	    'activeLoan', 
			        	    'loanInputs',
			        	    'loanTypes',
			        	    'operationType',
			        	    'cautionneurs',
			        	    'debitAccounts',
			        	    'creditAccounts',
			        	    'currentMemberId')
			    );
	}

	/**
	 * Get operation type 
	 * @param   $loanInputs 
	 * @param   $member     
	 * @return  string
	 */
    private function getOperationType($loanInputs,$member)
    {	
		if (isset($loanInputs['operation_type']) == false) {
			// If member has active loan make this into special loan
			if ($member->has_active_loan) {
				return 'special_loan';
			}

			return 'ordinary_loan';
		}
		return $loanInputs['operation_type'];
    }

	/**
     * Get default accounts for this modules
     * @return array 
     */
    public function getDefaultAccounts($operation_type)
    {
        switch ($operation_type) {
            case 'ordinary_loan':
				$defaultDebitsAccounts	=  DefaultAccount::with('accounts')->debit()->ordinaryLoan()->get();
				$defaultCreditsAccounts	= DefaultAccount::with('accounts')->credit()->ordinaryLoan()->get();
                break;
            case 'urgent_ordinary_loan':
				$defaultDebitsAccounts	=  DefaultAccount::with('accounts')->debit()->ordinaryLoan()->get();
				$defaultCreditsAccounts	=  DefaultAccount::with('accounts')->credit()->ordinaryLoan()->get();
                break;
            case 'special_loan':
				$defaultDebitsAccounts	=  DefaultAccount::with('accounts')->debit()->specialLoan()->get();
				$defaultCreditsAccounts	=  DefaultAccount::with('accounts')->credit()->specialLoan()->get();
                break; 
            case 'social_loan':
				$defaultDebitsAccounts	=  DefaultAccount::with('accounts')->debit()->socialLoan()->get();
				$defaultCreditsAccounts	=  DefaultAccount::with('accounts')->credit()->socialLoan()->get();
                break;   
             case 'emergency_loan':
				$defaultDebitsAccounts	=  DefaultAccount::with('accounts')->debit()->emergencyLoan()->get();
				$defaultCreditsAccounts	=  DefaultAccount::with('accounts')->credit()->emergencyLoan()->get();
                break;   

            default:
             return [
		            'debits' => [],
		            'credits' => [],
		        ];
			break;

        }
        
        
        $debitsAccounts = [];
        $creditsAccounts = [];

		foreach ($defaultDebitsAccounts as $defaultDebitAccount) 
		{
			foreach ($defaultDebitAccount->accounts as $account) 
			{
				$debitsAccounts[$account->id]	= $account->entitled;
			}
		}
	

		foreach ($defaultCreditsAccounts as $defaultCreditAccount) 
		{
            foreach ($defaultCreditAccount->accounts as $account) 
            {
                $creditsAccounts[$account->id] = $account->entitled;
            }
        }
        

        return [
            'debits' => (object) $debitsAccounts,
            'credits' => (object) $creditsAccounts
        ];
    }
	/**
	 * THIS SECTION HAS THE AJAX FUNCTIONS THAT ARE CALLED BY THE
	 * AJAX API ONLY
	 */
	/**
	 * Update a loan field
	 * @return  void
	 */
	public function ajaxFieldUpdate() {
		try
		{
		$this->loanFactory->addLoanInput(Input::all());

		$this->loanFactory->calculateLoanDetails();
		}
		catch(\Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * Stores in the session the debit accounts ids and credit accounts IDs
	 * @return void
	 */
	public function ajaxAccountingFeilds() {
		// Let's try to store what is submitted
		// in the session
		// If we have debit accounts in the submission
		// then try to set it in the session
		if (Input::has('debitAccounts')) {
			$this->loanFactory->setDebitsAccounts(Input::get('debitAccounts'), Input::get('debitAmounts'));
		}

		if (Input::has('creditAccounts')) {
			$this->loanFactory->setCreditAccounts(Input::get('creditAccounts'), Input::get('creditAmounts'));
		}

		$this->loanFactory->setBondedAmount();
	}
   

	/**
	 * Get loan by transaction ID
	 * @param  Ceb\Models\loan   $loan          
	 * @param  $transactionId 
	 * @return 
	 */
   public function process($loanId,$toSetstatus)
   {
   	    // First check if the user has the permission to do this
        if (!$this->user->hasAccess('loan.check.loan.status')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
    
    try {
        /** we can only allow to reject or approve a loan */
        if ((strtolower($toSetstatus) !== 'rejected') && (strtolower($toSetstatus) !== 'approved')) {
        	flash()->error(trans('loan.loan_can_either_be_approved_or_rejected'));
        	return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is checking loan status');	
		$loan = $this->loan->with('member')->unBlocked()->find($loanId);

		$user = $loan->member;

   		if (is_null($loan)) {
   			flash()->warning(trans('loan.we_could_not_find_the_loan_you_are_looking_for'));
   			return redirect()->back();
   		}
	

   		$transactionid 	= $loan->transactionid;
		// Start saving if something fails cancel everything
		Db::beginTransaction();

   		// If we have rejected this loan then delete it and all its items
  		$loan->status = strtolower($toSetstatus);

  		switch ($loan->status) {

  			case 'rejected': // If rejected delete everything
  				$loan->postings()->delete();
  				$isWellUpdated = $loan->delete();
  				DB::commit();
  				break;
  			
  			default:
		  		$isWellUpdated = $loan->save();
		  		if ($isWellUpdated == false) {
						flash()->warning(trans('loan.error_occured_while_trying_to_approve_this_loan'));
						DB::rollBack();
						return redirect()->back();
					}
		  		// Do also update all the postings
				foreach ($loan->postings as $posting) {
					$posting->status = strtolower($toSetstatus);
					if ($posting->save() == false) {
						flash()->warning(trans('loan.error_occured_while_trying_to_update_postings_related_to_this_loan'));
						DB::rollBack();
						return redirect()->back();
					}
				}

				// Lastly, Let's commit a transaction since we reached here
				DB::commit();
  				break;
  		}

	  	flash()->success(trans('loan.loan_with_transaction_id_'.$loan->transactionid.'_has_been_'.$loan->status));

  		if(($isWellUpdated == true) && (strtolower($toSetstatus) === 'approved' ))
  		{
  			
	  		/**
	   	     * @todo add option to  mark notification as read
	   	     * 
	   	     */
	   	      /** Notify the requestor */
        Notifynder::category('loan.approved')
                   ->from($this->user->id)
                   ->to($user->id)
                   ->url(route('members.show',['memberid'=>$user->id]))
                   ->send();
    
	   $data['names'] = $user->names;
	   $data['status'] = $toSetstatus;
	   Mail::queue('emails.loanstatus', $data, function ($message) use ($user) {
		    $message->to($user->email);
		    $message->subject("Loan status changed");
	   });
                   
  		return redirect()->route('reports.members.contracts.loan',['loanId'=>$user->adhersion_id]);
  	 }

  	}catch (\Exception $e){
  		Log::error($e);
  		flash()->error($e->getMessage());
  	}

  	return redirect()->route('loan.pending');
   	    
   }


   /**
    * Show pending loans 
    * @param  numeric $loanId optional showing one loan id
    * @return vie 
    */
   public function getPending($loanId = null)
   {
   	    // First check if the user has the permission to do this
        if (!$this->user->hasAccess('loan.can.approve.loan')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing pending loan');

        if (!is_null($loanId)) {
        	// we are looking for a special loan, let's grab it  	
	   		$loans = $this->loan->unBlocked()->where('id',$loanId)->orderBy('updated_at','DESC')->paginate(20);;
        }
        else
        {
	   		$loans = $this->loan->with('member.institution')->unBlocked()->orderBy('updated_at','DESC')->paginate(20);
        }

   	    return view('loansandrepayments.pending_loans',compact('loans'));
   }

   /**
    * Show blocked loans 
    * @param  numeric $loanId optional showing one loan id
    * @return vie 
    */
   public function getBlocked($loanId = null)
   {
   	    // First check if the user has the permission to do this
        if (!$this->user->hasAccess('loan.can.unblock.loan')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing blocked loan');

        if (!is_null($loanId)) {
        	// we are looking for a special loan, let's grab it  	
	   		$loans = $this->loan->blocked()->where('id',$loanId)->orderBy('updated_at','DESC')->paginate(20);
        }
        else
        {
	   		$loans = $this->loan->with('member.institution')->blocked()->orderBy('updated_at','DESC')->paginate(20);
        }

   	    return view('loansandrepayments.blocked_loans',compact('loans'));

   }

   /**
    * Show form to provide bank details of unblocking a loan
    * @param  numeric $loanid 
    * @return view       
    */
   public function showUnblockingForm($loanid=null)
   {
   		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('loan.can.unblock.loan')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        if (!is_null($loanid)) {
        	Session()->put('loan_id', $loanid);
        }

        $loanid = Session()->get('loan_id', null);
		
		$cautionneurs      = $this->loanFactory->getCautionneurs();
		$loan              = $this->loan->findOrFail($loanid);
		$member            =  $loan->member;

		// Set this members inn the session
		$this->loanFactory->setMember($member);

		$bonded_amount     = 0;
		$show_caution_form = false;

        /** Check if this loan should be guaranteed*/
        if (
	        	(($loan->loan_to_repay + $member->loan_balance) > $member->total_contribution) && // Ensure balance alloes this
	        	strtolower($loan->operation_type) !== 'emergency_loan' // Ensure we don't do it for emergencies
        	)  {
			$bonded_amount     = ($loan->loan_to_repay + $member->amount_to_bond);
			$show_caution_form = true;
        }

        // First log 
        Log::info($this->user->email . ' is viewing blocking form loan with id'.$loanid);

        $title = trans('loan.provide_bank_details_to_unblock_this_loan');
        return view('loansandrepayments.unblock_form',compact('loanid','title','loan','member','cautionneurs','bonded_amount','show_caution_form'));

   }

   /**
    * Unblock loan
    * @return
    */
   public function unblock(UnblockLoanRequest $request)
   {
   		// First check if the user has the permission to do this
        if (!$this->user->hasAccess('loan.can.unblock.loan')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

	    // First log 
	    Log::info($this->user->email . ' is  unblocking loan with details '.json_encode($request->all()));

	    /////////////////////////////////////
	    // Prepare the passed information  //
	    /////////////////////////////////////
	    $requestData = $request->all();
	  	$loanid = is_array($request->all()) ? $requestData['loanid'] : $request->get('loanid');
	  	
	    $loan = $this->loan->find($loanid);

	    /** If we cannot find the loan we are trying to unblock then display error */
	    if (is_null($loan)) {
	    	flash()->error(trans('loan.we_could_not_find_the_loan_you_are_trying_to_unlock'));
	    	return redirect()->route('loan.blocked');
	    }

        // Member cannot be cautionneur to him self, 
        // DISABLED BECAUSE CAUTIONNEUR ARE ON THE LOAN FORM
        // if (in_array($loan->adhersion_id,request()->only('cautionneur1','cautionneur2'))) {
        //     flash()->warning(trans('general.member_cannot_be_guarantor_to_himself'));
        // 	return $this->showUnblockingForm($loanid);
        // }

	    ////////////////////////////
	    // We are safe to go now  //
	    ////////////////////////////
	    
	    // Start saving if something fails cancel everything
		DB::beginTransaction();

		//  Update the loan only if the loan
		$loan->cheque_number = $request->get('cheque_number');
		$loan->bank_id       = $request->get('bank_id');
		$loan->status 		 = 'unblocked';
		$loan->movement_nature = Input::get('movement_nature', 	$loan->movement_nature);
		
		$saveLoan = $loan->save();

		$member            = $loan->member;
		$cautionneurs      = $this->loanFactory->getCautionneurs();
		$amount_bonded     = Input::get('amount_bonded', $member->amount_to_bond);
		$transactionid     = $loan->transactionid;
		$cautionneursSaved = $this->recordCautionneurs($transactionid,$loan,$member,$cautionneurs,$amount_bonded);

		if ($cautionneursSaved == false) {
			flash()->error('loan.we_could_not_save_cautionneurs_therefore_we_have_rollback_this_action');
			DB::rollBack();
			return redirect()->route('loan.blocked');
		}

		// If we cannot save this posting then rollback transaction
		if ($loan->postings->isEmpty()) {
			flash()->warning(trans('loan.we_could_not_find_postings_that_are_related_to_this_loan_therefore_this_operation_did_not_take_effect'));
				DB::rollBack();
				return redirect()->route('loan.blocked');
		}

		// Update posting
		foreach ($loan->postings as $posting) {
			$posting->cheque_number = $loan->cheque_number;
			$posting->bank 			= $loan->bank_id;

			// If we cannot save this posting then rollback transaction
			if ($posting->save() == false ) {
				flash()->warning(trans('loan.we_could_not_update_postings_that_are_related_to_this_loan_therefore_this_operation_did_not_take_effect'));
				DB::rollBack();
				return redirect()->route('loan.blocked');
			}
		}

		////////////////////////////////////////////////////////////////////////
		// Did we update loan with the bank information ? if no then rollback //
		////////////////////////////////////////////////////////////////////////
		if ($saveLoan == false) {
			flash()->error(trans('loan.we_could_not_unblock_the_loan_please_try_again'));
			DB::rollBack();
			return redirect()->route('loan.blocked');
		}

		//////////////////////////////////////////////////////////////
		// Lastly, Let's commit a transaction since we reached here //
		//////////////////////////////////////////////////////////////
		DB::commit();

		// We have completed all we need to do to unblock this loan,
		// Now lets clear some resources we have used
		$this->loanFactory->clearAll();

		 // Notify all people who has right to approve loan 
        // Get all users who have the right to approve leave
        // if we found them then ilitirate them and 
        // make sure, we notify all of them
        $groups = UserGroup::with('users')->get();

        foreach ($groups as $group) {      
            
            // If this group doesn't have access then 
            // go to the next group
            
            if (!$group->hasAccess('loan.can.approve.loan')) {
                continue;
            }

            // Group has access let's notify them
           foreach ($group->users as $user) {
               Notifynder::category('loan.approval')
                   ->from($this->user->id)
                   ->to($user->id)
                   ->url(route('loan.pending',['loanid'=>$loan->id]))
                   ->send();
           }
		}

		$user = $loan->member;
    
	   $data['names'] = $user->names;

	   $data['status'] = $loan->status;
	   Mail::queue('emails.loanstatus', $data, function ($message) use ($user) {
		    $message->to($user->email);
		    $message->subject("Loan status changed");
	   });
		flash()->success(trans('loan.loan_successfully_unblocked'));

		return redirect()->route('loan.blocked');
   }



   /**
	 * Record cautionneur details for this loan
	 * @param  string $transactionid 
	 * @param  string $loanId        
	 * @return bool         
	 */
	public function recordCautionneurs($transactionid,$loan,$member,$cautionneurs,$amount_bonded)
	{
		// Devide amount equally 
		$cautionneursCount = count($cautionneurs);

		// If we don't have cautinneurs, just pass the method
		if ($cautionneursCount == 0 ) {
			return true;
		}

		$amount  = $amount_bonded / count($cautionneurs);

		foreach ($cautionneurs as $cautionneur) 
		{
				$memberLoanCautionneur = new MemberLoanCautionneur;
				$memberLoanCautionneur->member_adhersion_id       = $member->adhersion_id;
				$memberLoanCautionneur->cautionneur_adhresion_id  = $cautionneur->adhersion_id;
				$memberLoanCautionneur->amount                    = $amount;
				$memberLoanCautionneur->transaction_id			  = $transactionid;
				$memberLoanCautionneur->loan_id                   = $loan->id;
				$memberLoanCautionneur->letter_date			  	  = $loan->letter_date;

				// Fail transaction if something went wrong
				if(!$memberLoanCautionneur->save())
				{
					return false;
				}
		}

		return true;
	}
}