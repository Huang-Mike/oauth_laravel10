<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\OtpMatchRule;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\CodeController;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class AuthenticationRequest extends FormRequest
{
    protected OtpController $otpCon;
    protected CodeController $codeCon;
    protected OtpMatchRule $otpMatchRule;

    public function __construct (
        OtpController $otpCon,
        CodeController $codeCon,
        OtpMatchRule $otpMatchRule
    )
    {
        $this->otpCon = $otpCon;
        $this->codeCon = $codeCon;
        $this->otpMatchRule = $otpMatchRule;
    }

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
        $validate = [];
        $routeName = $this->route()->getName();
        $routeArray = explode('.', $routeName);
        $code = $this->codeCon->isAvailable($_GET['code']);
        
        if ($code) {
            $client = $code->client;
            $config = json_decode($client->config, true);
            if (in_array('register', $routeArray)) {
                $validate = [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255'],
                    'phone' => ['required', 'string']
                ];
                $validate[$config['auth']['verify_type']] = ['required'];
                if ($config['auth']['verify_type'] == 'password') {
                    array_push($validate[$config['auth']['verify_type']], 'confirmed', Password::defaults());
                    $user = User::where('client_id', $client->id)->where($config['auth']['primary_key'], $_POST[$config['auth']['primary_key']])->first();
                    if ($user) {
                        array_push($validate[$config['auth']['primary_key']], 'unique:users');
                    }
                }
            } else {
                $validate[$config['auth']['primary_key']] = ['required'];
                $validate[$config['auth']['verify_type']] = ['required'];
            }

            /* Verify OTP. */
            if ($config['auth']['verify_type'] == 'otp') {
                array_push($validate['otp'], $this->otpMatchRule);
            }
        }

        return $validate;
    }

    public function messages()
    {
        return [
            'email.unique' => __("validation.unique", ['attribute' => __("client-form.email")]),
            'password.confirmed' => __("validation.confirmed", ['attribute' => __("client-form.password")]),
            'password.min' => __("validation.min.string", ['attribute' => __("client-form.password")]),
        ];
    }
}
