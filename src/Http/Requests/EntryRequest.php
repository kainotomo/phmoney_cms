<?php

namespace Kainotomo\PHMoney\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntryRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'date_entered' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'action' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255'],
            'quantity_num' => ['required', 'integer'],
            'quantity_denom' => ['required', 'integer'],
            'i_acct' => ['nullable', 'exists:Kainotomo\PHMoney\Models\Account,guid'],
            'i_price_num' => ['nullable', 'integer'],
            'i_price_denom' => ['nullable', 'integer'],
            'i_discount_num' => ['nullable', 'integer'],
            'i_discount_denom' => ['nullable', 'integer'],
            'invoice' => ['nullable', 'exists:Kainotomo\PHMoney\Models\Invoice,guid'],
            'i_disc_type' => ['nullable', 'string', 'max:255'],
            'i_disc_how' => ['nullable', 'string', 'max:255'],
            'i_taxable' => ['nullable', 'boolean'],
            'i_taxincluded' => ['nullable', 'boolean'],
            'i_taxtable' => ['nullable', 'exists:Kainotomo\PHMoney\Models\Taxtable,guid'],
            'b_acct' => ['nullable', 'exists:Kainotomo\PHMoney\Models\Account,guid'],
            'b_price_num' => ['nullable', 'integer'],
            'b_price_denom' => ['nullable', 'integer'],
            'bill' => ['nullable', 'exists:Kainotomo\PHMoney\Models\Bill,guid'],
            'b_taxable' => ['nullable', 'boolean'],
            'b_taxincluded' => ['nullable', 'boolean'],
            'b_taxtable' => ['nullable', 'exists:Kainotomo\PHMoney\Models\Taxtable,guid'],
            'b_paytype' => ['nullable', 'integer'],
            'billable' => ['nullable', 'boolean'],
            'billto_type' => ['nullable', 'string'],
            'billto_guid' => ['nullable', 'string'],
            'billto_order' => ['nullable', 'string'],
        ];
    }
}
