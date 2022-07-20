<?php

namespace Kainotomo\PHMoney\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SplitRequest extends FormRequest
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
            'account_guid' => ['required', 'exists:Kainotomo\PHMoney\Models\Account,guid'],
            'memo' => ['string', 'max:2048', 'nullable'],
            'action' => ['string', 'max:2048', 'nullable'],
            'reconcile_state' => ['string', 'max:1'],
            'value_num' => ['integer', 'required'],
            'value_denom' => ['integer', 'required'],
            'quantity_num' => ['integer', 'required'],
            'quantity_denom' => ['integer', 'required'],
            //'debit' => ['numeric', Rule::requiredIf(is_null($request->credit)), 'nullable'],
            //'credit' => ['numeric', Rule::requiredIf(is_null($request->debit)), 'nullable'],
            'lot_guid' => ['nullable', 'exists:Kainotomo\PHMoney\Models\Lot,guid'],
        ];
    }
}
