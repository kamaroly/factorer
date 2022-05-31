<?php

namespace Ceb\Http\Requests;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Ceb\Factories\RegularisationFactory;
use Ceb\Http\Requests\Request;
use Ceb\Models\Setting;
use Ceb\Models\User;

class RegularisationRequest extends Request
{
     /**
     * @var Ceb\Factories\LoanFactory
     */
    protected $regularisationFactory;
    function __construct(RegularisationFactory $regularisationFactory,User $member,Setting $setting) {
        $this->regularisationFactory = $regularisationFactory;
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

      //Continue with Rule validation
      $rules = [
          'adhersion_id'               =>  'required|numeric',
          'operation_type'             =>  'required|min:3',
          'right_to_loan'              =>  'required|numeric',
          'contributions'              =>  'required|numeric',
          'balance_of_loan'            =>  'required|numeric',
          'interests_to_pay'           =>  'required|numeric',
          'wording'                    =>  'required|min:6',
          // 'movement_nature'            =>  'required',
          'debit_accounts'             =>  'required',
          'credit_accounts'            =>  'required',
          'debit_amounts'              =>  'required',
          'credit_amounts'             =>  'required',
          'accounting_amount'          =>  'required|confirmed|numeric|min:'.$attributes['accounting_amount'],
          'ragularisationccountamount' =>  'required|confirmed|numeric',
          'cautionneur'                =>  'confirmed',
          'accounts'                   =>  'confirmed',
        ];

      if (strpos($attributes['operation_type'],'installment') !== false) {
        $rules['new_installments']        = 'required|numeric';
        $rules['additional_installments'] = 'required|numeric';
      }

      if (strpos($attributes['operation_type'],'amount') !== false) {

        $rules['additional_amount' ] = 'required|numeric|max:'.$attributes['right_to_loan'];
        $rules['amount_received']    = 'required|numeric';
        $rules['additional_amount']  = 'required|numeric';
      }

       if (strtolower($attributes['operation_type']) == 'installment') {
          $rules['reference_number']   = 'required';
        }       
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
        if (is_null($attributes['adhersion_id']) || empty($attributes['adhersion_id'])) {
          return $attributes;
        }


          // Set the member by his adhersion ID
          $memberId = $this->member->findByAdhersion($attributes['adhersion_id'])->id;
          $this->regularisationFactory->addMember($memberId);

           // Modify or Add new array key/values
           // Make sure these fields are numeric
           // ==================================
           $attributes['right_to_loan']                  = (int)  str_replace(',','',$attributes['member']['right_to_loan']);
           $attributes['contributions']                  = (int) str_replace(',', '', $attributes['member']['contributions']);
           $attributes['balance_of_loan']                = (int) str_replace(',', '', $attributes['member']['balance_of_loan']);

           // If the operation type is installement then record interests only as the accounting amount
           if (strtolower($attributes['operation_type']) == 'installments') {
              $attributes['accounting_amount']              = intval($attributes['interests_to_pay']);
           }
           else // This is another type of regulation and it has additional amount, let's record addition amount in accounting
           {
              $attributes['accounting_amount']              = intval($attributes['additional_amount']);
           }
            // Validating account amount
           $attributes['accounting_amount']              = array_sum($attributes['debit_amounts']);
           $attributes['accounting_amount_confirmation'] = array_sum($attributes['credit_amounts']);
           
           // Validate total amount vs Account amount   
           $attributes['ragularisationccountamount']              = $attributes['accounting_amount'];
           $attributes['ragularisationccountamount_confirmation'] = $attributes['accounting_amount_confirmation'];
           
           // Validate the input accounts 
           $accounts                                     = array_intersect($attributes['credit_accounts'], $attributes['debit_accounts']);
           if (count($accounts) > 0) {    
           $attributes['accounts']                       = count($accounts);
           $attributes['accounts_confirmation']          = 0;
           }
           
           // Validate bonded amount
           // $bondedAmount                                 = (int) $attributes['amount_bonded'];
           $contributions                                = (int) str_replace(',', '', $attributes['member']['contributions']);
           $attributes['amount_bonded']                  = 0;
           // If we have bonded amount then check if we have cautionneur
           // If the amount to repay is higher than total contributions  
           // we need to have a cautionneur
           //   if (!empty($bondedAmount) && ($attributes['additional_amount'] > $attributes['right_to_loan']  )) {
           //   // If we have bonded amount make sure we fail this transacation            
           //   $cautionneur                                  = $this->regularisationFactory->getCautionneurs();
           //   //  We can only allow two cautionneurs if they are not set 
           //   //  We will fail this validation
           //   if (count($cautionneur) !== 2) 
           //   {
           //     $attributes['cautionneur']                    = 'cautionneur';
           //     $attributes['cautionneur_confirmation']       = 'cautionneur_to_faile';
           //   }
           // }
           // else
           // {
           // $attributes['amount_bonded']                  = 0;
           // }
          
        // Format/sanitize data here
        return $attributes;
    }
}
