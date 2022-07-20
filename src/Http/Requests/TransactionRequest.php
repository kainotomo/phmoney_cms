<?php

namespace Kainotomo\PHMoney\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'guid' => ['string', 'required', 'max:32'],
            'currency_guid' => ['required', 'exists:Kainotomo\PHMoney\Models\Commodity,guid'],
            'num' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'post_date' => ['required', 'date'],
            'enter_date' => ['required', 'date'],
            'splits' => ['required', 'array'],
        ];
    }
}
