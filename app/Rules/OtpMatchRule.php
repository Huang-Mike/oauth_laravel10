<?php

namespace App\Rules;

use App\Http\Controllers\OtpController;
use Illuminate\Contracts\Validation\Rule;

class OtpMatchRule implements Rule
{
    protected OtpController $otpCon;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(OtpController $otpCon)
    {
        $this->otpCon = $otpCon;
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
        return $this->otpCon->confirm($_GET['code'], $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __("validation.matched", ['attribute' => __("client-form.otp")]);
    }
}
