<?php

namespace Kainotomo\PHMoney\Http\Requests\Export;

use Illuminate\Foundation\Http\FormRequest;

class DownloadRequest extends FormRequest
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
            'accounts' => ['required', 'array'],
            'date_start' => ['required'],
            'date_end' => ['required'],
        ];
    }
}
