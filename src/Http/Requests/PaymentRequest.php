<?php

namespace Kainotomo\PHMoney\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            'item_guid' => ['required', 'string', 'max:255'],
            'transfer_account_guid' => ['required', 'exists:Kainotomo\PHMoney\Models\Account,guid'],
            'post_account_guid' => ['required', 'exists:Kainotomo\PHMoney\Models\Account,guid'],
            'post_date' => ['required', 'date'],
            'credit' => ['required', 'numeric'],
            'debit' => ['required', 'numeric'],
            'num' => ['nullable', 'string', 'max:255'],
            'memo' => ['nullable', 'string', 'max:255'],
        ];
    }
}
