<?php

namespace Kainotomo\PHMoney\Http\Requests\Teams;

use Illuminate\Foundation\Http\FormRequest;

class OptionsRequest extends FormRequest
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
            'options.accounting_period' => ['array'],
            'options.accounting_period.date_start' => ['nullable'],
            'options.accounting_period.date_end' => ['nullable'],
            'options.business' => ['array'],
            'options.business.company_name' => ['nullable', 'string', 'max:255'],
            'options.business.company_address' => ['nullable', 'string', 'max:255'],
            'options.business.company_contact_person' => ['nullable', 'string', 'max:255'],
            'options.business.company_phone_number' => ['nullable', 'string', 'max:255'],
            'options.business.company_fax_number' => ['nullable', 'string', 'max:255'],
            'options.business.company_email_address' => ['nullable', 'string', 'max:255'],
            'options.business.company_website_url' => ['nullable', 'string', 'max:255'],
            'options.business.company_id' => ['nullable', 'string', 'max:255'],
            'options.business.default_customer_taxtable.guid' => ['nullable', 'exists:Kainotomo\PHMoney\Models\Taxtable,guid'],
            'options.business.default_vendor_taxtable.guid' => ['nullable', 'exists:Kainotomo\PHMoney\Models\Taxtable,guid'],
            'options.tax' => ['array'],
            'options.tax.tax_number' => ['nullable', 'string', 'max:255'],
            'options.counters' => ['array'],
            'options.counters.bill' => ['nullable', 'integer', "min:0"],
            'options.counters.vendor' => ['nullable', 'integer', "min:0"],
            'options.counters.invoice' => ['nullable', 'integer', "min:0"],
            'options.counters.job' => ['nullable', 'integer', "min:0"],
            'options.counters.employee' => ['nullable', 'integer', "min:0"],
            'options.counter_formats' => ['array'],
            'options.counter_formats.bill' => ['nullable', 'string', 'max:255'],
            'options.counter_formats.vendor' => ['nullable', 'string', 'max:255'],
            'options.counter_formats.invoice' => ['nullable', 'string', 'max:255'],
            'options.counter_formats.job' => ['nullable', 'string', 'max:255'],
            'options.counter_formats.employee' => ['nullable', 'string', 'max:255'],
        ];
    }
}
