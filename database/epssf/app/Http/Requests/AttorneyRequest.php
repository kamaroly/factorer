<?php

namespace Ceb\Http\Requests;

use Ceb\Http\Requests\Request;
use Sentry;
class AttorneyRequest extends Request
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
        return [
            'names'=> 'required',
            'photo'=> 'required',
            'nid'   => 'required',
            'signature' => 'required'
        ];
    }
}
