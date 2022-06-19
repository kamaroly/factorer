<?php

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class AccountingRequest extends Request {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return auth()->check();
	}

	   /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

      //Continue with Rule validation
        return [
          'journal'   		 		 =>  'numeric|min:1',
          'wording'                  =>  'required|min:5',
          'cheque_number'            =>  'min:10|unique:postings',
          'debit_amounts'   		 =>  'required|min:1',
          'credit_amounts'           =>  'required|min:1',
          'credit_accounts'          =>  'required',
          'debit_accounts'           =>  'required',
          'accounting_amount' 		 =>  'required|confirmed|numeric:0',
          'inputaccounts' 		 	 =>  'required|numeric|max:0',
        ];
    }

    /**
     * Modifying input before validation
     * @return array
     */
    public function all($keys = NULL)
    {
        // Grab all inputs from the user
        $attributes = parent::all($keys);

        // Modify or Add new array key/values
        // ==================================

        // Validating account amount
        $attributes['accounting_amount'] = array_sum($attributes['debit_amounts']);
        $attributes['accounting_amount_confirmation'] = array_sum($attributes['credit_amounts']);

        // Let's check if the user is trying to debit and credit
        // Same account, which is not allowed as per accounting laws
    	$attributes['inputaccounts'] = count(array_intersect($attributes['debit_accounts'], $attributes['credit_accounts']));

        // Format/sanitize data here
        return $attributes;
    }
}
