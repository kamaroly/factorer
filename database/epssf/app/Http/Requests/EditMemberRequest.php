<?php

namespace Ceb\Http\Requests;

use Ceb\Http\Requests\Request;

class EditMemberRequest extends Request {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return \Sentry::check();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {

	return [
		'first_name'            =>'required',
	    'last_name'            =>'required',
		'district'         =>'required',
		'province'         =>'required',
		'institution_id'   =>'required|numeric',
		'service'          =>'required',
		'termination_date' =>'date',
		'employee_id'      =>'alpha_num',
		// 'password'      =>'required',
		'date_of_birth'    =>'date',
		'sex'              =>'required',		
		'member_nid'       =>'required|numeric',
		// 'nationality'   =>'required',
		'email'            =>'email',
		'telephone'        =>'min:9|max:12',
		'monthly_fee'      =>'required',
		'photo'            =>'image',
		'signature'        =>'image'
        ];
	}

	public function all()
	{
        // Grab all inputs from the user
        $attributes = parent::all();

        // Clean NID
		$attributes['member_nid'] = removeSpaces($attributes['member_nid']);

		return $attributes;
	}
}
