<?php

namespace Kainotomo\PHMoney\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
            'date_opened' => ['required', 'date'],
            'terms' => ['nullable', 'exists:Kainotomo\PHMoney\Models\Billterm,guid'],
            'owner_guid' => ['required', 'string'],
        ];
    }
}
