<?php

namespace Kainotomo\PHMoney\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoicePostRequest extends FormRequest
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
            'date_posted' => ['required', 'date'],
            'date_due' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'account_guid' => ['required', 'exists:Kainotomo\PHMoney\Models\Account,guid'],
            'accumulate' => ['required', 'boolean'],
        ];
    }
}
