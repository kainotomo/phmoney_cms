<?php

namespace Kainotomo\PHMoney\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxtableRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'taxtableentry.account' => ['required', 'exists:Kainotomo\PHMoney\Models\Account,guid'],
            'taxtableentry.amount_num' => ['required', 'integer'],
            'taxtableentry.amount_denom' => ['required', 'integer'],
            'taxtableentry.type' => ['required', 'integer'],
        ];
    }
}
