<?php
namespace Ceb\Http\Requests;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Ceb\Factories\LoanFactory;
use Ceb\Http\Requests\Request;
use Ceb\Models\Setting;
use Ceb\Models\User;
class CompleteLoanRequest extends Request
{
    /**
     * @var Ceb\Factories\LoanFactory
     */
    protected $loanFactory;
    function __construct(LoanFactory $loanFactory,User $member,Setting $setting) {
        $this->loanFactory = $loanFactory;
        $this->member = $member;
        $this->setting = $setting;
    }
    public function authorize()
    {
        return Sentry::check();
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Only validate for other method other than get
        if ($this->isMethod('get')) {
           return [];
        }
        $attributes = $this->all();
        
        $rightToLoan = $this->member->findByAdhersion($attributes['adhersion_id']);

        // If this member exists then get his right to loan 
        if (!empty($rightToLoan)) {
          $rightToLoan = $rightToLoan->right_to_loan;
        }

        // Validate the right to loan
        $settingKey = strtolower($attributes['operation_type']).'.amount';
        if ($this->setting->hasKey($settingKey) !== false) {
           $rightToLoan = $this->setting->keyValue($settingKey);
        }
     
      //Continue with Rule validation
        $rules =  [
          'adhersion_id'      =>  'required',
          'operation_type'    =>  'required|min:3',
          'loan_to_repay'     =>  'required|numeric|min:5000|max:'.$rightToLoan,
          'wording'           =>  'required|min:6',
          'accounting_amount' =>  'required|confirmed|numeric|min:'.parent::get('amount'),
          'loanaccountamount' =>  'required|confirmed|numeric',
          'cautionneur'       =>  'confirmed',
          'accounts'          =>  'confirmed',
        ];

        return $rules;
    }
    /**
     * Modifying input before validation
     * @return array
     */
    public function all()
    {
        // Grab all inputs from the user
        $attributes = parent::all();

        // Continue only if the method is get 
         if ($this->isMethod('get')) {
           return $attributes;
        }

        // Set the member by his adhersion ID
        $memberId = $this->member->findByAdhersion($attributes['adhersion_id']);

        // If we cannot find a member with this adhersion id finish the flow from here
        if (is_null($memberId) && empty($memberId)) {
          return $attributes;
        }

        $memberId = $memberId->id;

        $this->loanFactory->addMember($memberId);
        // Modify or Add new array key/values
        // ==================================
        // Make sure these fields are numeric
      
       $attributes['right_to_loan']                  = (int) str_replace(',','',$attributes['member']['right_to_loan']);
       $attributes['contributions']                  = (int) str_replace(',', '', $attributes['member']['contributions']);
       $attributes['balance_of_loan']                = (int) str_replace(',', '', $attributes['member']['balance_of_loan']);

        $attributes['loan_to_repay']  = intval($attributes['loan_to_repay']) ;
        // Validating account amount
        $attributes['accounting_amount'] = array_sum($attributes['debit_amounts']);
        $attributes['accounting_amount_confirmation'] = array_sum($attributes['credit_amounts']);
        
        // Validate total amount vs Account amount   
        $attributes['loanaccountamount'] = $attributes['loan_to_repay'];
        $attributes['loanaccountamount_confirmation'] = $attributes['accounting_amount_confirmation'];
          
        // Validate the input accounts 
        $accounts = array_intersect($attributes['credit_accounts'], $attributes['debit_accounts']);
        if (count($accounts) > 0) {    
            $attributes['accounts']              = count($accounts);
            $attributes['accounts_confirmation'] = 0;
        }
        
        // Validate bonded amount.
        // If we dont have amount bonded then  use zero as default amount before proceeding.
        // This is a fix for undefined index amount_bonded error
        $bondedAmount = isset($attributes['amount_bonded']) ? (int) $attributes['amount_bonded'] : 0 ;
        $attributes['amount_bonded'] = 0;
        $contributions = (int) str_replace(',', '', $attributes['member']['contributions']);
        // If we have bonded amount then check if we have cautionneur
        // If the amount to repay is higher than total contributions  
        // we need to have a cautionneur
        if (!empty($bondedAmount) && ($attributes['loan_to_repay'] > $contributions )) {
            // If we have bonded amount make sure we fail this transacation            
            $cautionneur = $this->loanFactory->getCautionneurs();
            //  We can only allow two cautionneurs if they are not set 
            //  We will fail this validation
            if (count($cautionneur) !== 2) {
                 $attributes['cautionneur']                 = 'cautionneur';
                 $attributes['cautionneur_confirmation']    = 'cautionneur_to_faile';
            }
        }
        
        // Format/sanitize data here
        return $attributes;
    }
}