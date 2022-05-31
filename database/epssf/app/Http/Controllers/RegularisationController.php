<?php
namespace Ceb\Http\Controllers;

use Ceb\Factories\RegularisationFactory;
use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests\RegularisationRequest;
use Ceb\Models\DefaultAccount;
use Ceb\Models\Loan;
use Ceb\Models\User as Member;
use Ceb\Models\User;
use Ceb\Models\UserGroup;
use Fenos\Notifynder\Facades\Notifynder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Input;
use Redirect;
use Session;
use Exception;

class RegularisationController extends Controller {
    protected $loanId = 0;
    protected $currentMember = null;
    protected $regularisationFactory;
    protected $member;
    protected $loan;

    function __construct(RegularisationFactory $regularisationFactory,Member $member,Loan $loan) {
    
        $this->member = $member;
        $this->regularisationFactory = $regularisationFactory;
        $this->loan = $loan;
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
                $this->regularisationFactory->addLoanInput([$key =>$value]);
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
        
        try {
        // First log 
        Log::info($this->user->email . ' is adding member to loan form');
        // Add the select member to the session
        $this->regularisationFactory->addMember($id);
        } catch (Exception $e) {
            flash()->error($e->getMessage());
        }

        return $this->reload();
    }

    /**
     * Complete loan transaction
     * @return mixed
     */
    public function complete(RegularisationRequest $request) {


        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('loan.complete.loan.request')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is completing loan request');
        // Make sure we update with latest form inputs
        $this->regularisationFactory->addLoanInput($request->all());

        // Update accounting fields too
        $this->ajaxAccountingFeilds();

        // Only complete transaction when someone does a post request
        if ($request->isMethod('post')) {
            // Complete transaction
            if ($loanId = $this->regularisationFactory->complete()) {

                $message = trans('loan.regularisation_completed');
                $this->loanId = $loanId;
                flash()->success($message);

                // If this user doesn't have right to view the contract
                // Then show him an error
                if (!$this->user->hasAccess('reports.contract.loan')) {
                    flash()->warning(trans('loan.regularisation_completed_but_we_didnot_show_you_contract_because_you_do_not_have_the_right_to_view_it'));
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
    
        $this->regularisationFactory->cancel();
        $message = trans('loan.regularisation_cancelled');
        flash()->success($message);

        return Redirect::route('regularisation.index');
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

        // First log 
        Log::info($this->user->email . ' is setting loan cautionneur');
    
        if($this->regularisationFactory->setCautionneur(Input::all()))
        {           
            flash()->success(trans('loan.cautionneur_has_been_added_successfully'));
        }

        return $this->reload();
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
    
        $this->regularisationFactory->removeCautionneur($cautionneur);
        return $this->reload();
    }

    /**
     * Reload loan page
     * @return mixed
     */
    private function reload($loanId = 0) {

        $member     = $this->regularisationFactory->getMember();
        $loanInputs = $this->regularisationFactory->getLoanInputs();

        // Set default to installment if operation type
        // does not exists
        if(!isset($loanInputs['operation_type'])){
            $loanInputs['operation_type'] = 'installments';
        }

        $creditAccounts  = $this->regularisationFactory->getCreditAccounts();
        $debitAccounts   = $this->regularisationFactory->getDebitAccounts();
        $cautionneurs    = $this->regularisationFactory->getCautionneurs();
        $operationType   = $this->regularisationFactory->getOperationType();
        $currentMemberId = $this->currentMember;
        
        $activeLoan = $this->loan;
        $rightToLoan = 0;

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
             $activeLoan  = $member->latestLoan();
        }

        /** DETERMINE WHICH DEFAULT ACCOUNTS TO SET */
        
        $defaultAccounts = $this->getDefaultAccounts($operation_type);

        return view('regularisation.index', 
               compact('member',
                       'loanId',
                       'activeLoan',
                       'loanInputs',
                       'rightToLoan',
                       'cautionneurs',
                       'operationType',
                       'debitAccounts',
                       'creditAccounts',
                       'defaultAccounts',
                       'currentMemberId'
                   )
            );
    }

    /**
     * Get default accounts for this modules
     * @return array 
     */
    public function getDefaultAccounts($operation_type)
    {
        switch ($operation_type) {
            case 'installments':
                $defaultDebitsAccounts =  DefaultAccount::with('accounts')->debit()->regularisationInstallments()->get();
                $defaultCreditsAccounts = DefaultAccount::with('accounts')->credit()->regularisationInstallments()->get();
                break;
            case 'amount':
                $defaultDebitsAccounts =  DefaultAccount::with('accounts')->debit()->regularisationAmount()->get();
                $defaultCreditsAccounts = DefaultAccount::with('accounts')->credit()->regularisationAmount()->get();
                break; 
            case 'amount_installments':
                $defaultDebitsAccounts =  DefaultAccount::with('accounts')->debit()->regularisationMountInstallments()->get();
                $defaultCreditsAccounts = DefaultAccount::with('accounts')->credit()->regularisationMountInstallments()->get();
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
                $debitsAccounts[$account->id]   = $account->entitled;
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
        
        $this->regularisationFactory->addLoanInput(Input::all());

        $this->regularisationFactory->calculateLoanDetails();
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
            $this->regularisationFactory->setDebitsAccounts(Input::get('debitAccounts'), Input::get('debitAmounts'));
        }

        if (Input::has('creditAccounts')) {
            $this->regularisationFactory->setCreditAccounts(Input::get('creditAccounts'), Input::get('creditAmounts'));
        }

        $this->regularisationFactory->setBondedAmount();
    }
   
}