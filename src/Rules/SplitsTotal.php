<?php

namespace Kainotomo\PHMoney\Rules;

use Illuminate\Contracts\Validation\Rule;

class SplitsTotal implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $total = 0;
        foreach ($value as $split) {
            $total += $split['value_num'] / $split['value_denom'];
        }
        return ($total == 0) ? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The sum of all splits must be zero';
    }
}
