<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AllowedTripletCount implements Rule
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
        return (in_array($value, [7, 9, 13, 15, 19, 21, 25, 27]));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Only image sets with 7, 9, 13, 15, 19, 21, 25 or 27 images (excluding original/reference)
          is supported for Triplet Comparison.';
    }
}
