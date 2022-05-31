<?php
namespace Ceb\Http\Requests;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Ceb\Factories\LoanFactory;
use Ceb\Http\Requests\Request;
use Ceb\Models\Setting;
use Ceb\Models\User;
use Ceb\Models\Loan;
class UnblockLoanRequest extends Request
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
      if ($this->isMethod('get')) {
           return [];
        }
      $rules = [
              'cheque_number'     =>  'required|unique:loans',
              'bank_id'           =>  'required|min:1',
              'loanid'            =>  'required|numeric',
              ];
      // If loan taken is higher than total contribution then make 
      // Cautionneur mandatory
      $attributes = $this->all();
      $loan = Loan::findOrFail($attributes['loanid']);
      $member = $loan->member;

      //////////////////////////////////////////////////////////////////////////////
      // DISABLED CAUTIONNEUR ON UNBLOCKING FORM BECAUSE IT'S ON THE FIRST SCREEN //
      //////////////////////////////////////////////////////////////////////////////
      // if ($loan->loan_to_repay > $member->total_contribution && $loan->operation_type !='emergency_loan') {
      //    $rules['cautionneur1']    =  'required';
      //    $rules['cautionneur2']    =  'required';
      //    $rules['amount_bonded']   =  'required|numeric';
      // }
      return $rules;
       
    }
   
    public function all()
    {
        $attributes = parent::all();
        $attributes['amount_bonded'] = isset($attributes['amount_bonded'])?$attributes['amount_bonded']:0;
        $bondedAmount = (int) $attributes['amount_bonded'];
        
        // If we have bonded amount then check if we have cautionneur
        // If the amount to repay is higher than total contributions  
        // we need to have a cautionneur
 
      return  array_change_key_case($attributes,CASE_LOWER);
    }
}
