<?php

namespace Modules\Receivings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceivingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
                "item_name"           => "required",
                "item_sku"            => "required",
                "item_qty"            => "required|numeric|min:1",
                "item_selling_price"  => "required|numeric",
                "item_buying_price"   => "required|numeric",
                "grouping"            => "required",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
