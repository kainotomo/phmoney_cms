<?php

namespace Kainotomo\PHMoney\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClosebookRequest extends FormRequest
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
            'post_date' => ['required', 'date'],
            'income_account_guid' => ['required', 'exists:Kainotomo\PHMoney\Models\Account,guid'],
            'expense_account_guid' => ['required', 'exists:Kainotomo\PHMoney\Models\Account,guid'],
            'string' => ['nullable', 'string', 'max:255'],
        ];
    }
}
