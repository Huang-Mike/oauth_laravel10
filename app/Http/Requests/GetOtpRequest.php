<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\CodeController;
use Illuminate\Foundation\Http\FormRequest;

class GetOtpRequest extends FormRequest
{
    protected OtpController $otpCon;
    protected CodeController $codeCon;

    public function __construct (
        OtpController $otpCon,
        CodeController $codeCon
    )
    {
        $this->otpCon = $otpCon;
        $this->codeCon = $codeCon;
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
        $comesFrom = self::previous_route();
        $code = $this->codeCon->isAvailable($_GET['code']);

        if ($code) {
            $client = $code->client;
            $config = json_decode($client->config, true);
            if ($comesFrom == 'register') {
                $validate = [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255'],
                    'phone' => ['required', 'string']
                ];
                $user = User::where('client_id', $client->id)->where($config['auth']['primary_key'], $_POST[$config['auth']['primary_key']])->first();
                if ($user) {
                    array_push($validate[$config['auth']['primary_key']], 'unique:users');
                }
            } else {
                $validate[$config['auth']['primary_key']] = ['required'];        
            }
        }

        return $validate;
    }

    private function previous_route()
    {
        $previousRequest = app('request')->create(app('url')->previous());
        return app('router')->getRoutes()->match($previousRequest)->getName();
    }

    public function messages()
    {
        return [
            'email.unique' => __("validation.unique", ['attribute' => __("client-form.email")]),
            'phone.unique' => __("validation.unique", ['attribute' => __("client-form.phone")])
        ];
    }
}
