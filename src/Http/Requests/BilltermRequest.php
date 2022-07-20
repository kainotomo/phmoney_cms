<?php

namespace Kainotomo\PHMoney\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BilltermRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'duedays' => ['required', 'integer'],
            'discountdays' => ['required', 'integer'],
            'discount_num' => ['required', 'integer'],
            'discount_denom' => ['required', 'integer'],
            'cutoff' => ['nullable', 'integer'],
        ];
    }
}
