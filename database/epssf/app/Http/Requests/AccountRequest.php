<?php

namespace Ceb\Http\Requests;

use Ceb\Http\Requests\Request;
use Sentry;
class AccountRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Sentry::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account_number'  => 'required|numeric|min:2|unique:accounts',
            'entitled'        => 'required|min:3|unique:accounts',
            'account_nature' => 'required|min:3',
        ];
    }
}
