<?php

namespace Ceb\Http\Requests;

use Ceb\Http\Requests\Request;
use Sentry;
class CompleteMemberTransactionRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
       $attributes = $this->all();

      //Continue with Rule validation
    $rules =  [
          'movement_type'    =>  'required|min:1',
          'operation_type'   =>  'required|min:3',
          'amount'           =>  'required|numeric',
          'wording'          =>  'required|min:6',
          'cheque_number'    =>  'required|alpha_dash|min:5',
          'bank'             =>  'required|min:1',
          'accounting_amount' => 'required|confirmed|numeric|min:'.parent::get('amount'),
          'transactionamount' => 'required|confirmed|numeric',
        ];

        if ($attributes['operation_type'] == 'remainers') {
            $rules['contract_number'] = 'required|min:3';
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


        // Modify or Add new array key/values
        // ==================================
        // Make sure these fields are numeric
         $attributes['amount']  = intval($attributes['amount']) ;

        // Validating account amount
        $attributes['accounting_amount'] = array_sum($attributes['debit_amounts']);
        $attributes['accounting_amount_confirmation'] = array_sum($attributes['credit_amounts']);
        
        // Validate total amount vs Account amount   
        $attributes['transactionamount'] = $attributes['amount'];
        $attributes['transactionamount_confirmation'] = $attributes['accounting_amount_confirmation'];

        // Format/sanitize data here
        return $attributes;
    }
}
