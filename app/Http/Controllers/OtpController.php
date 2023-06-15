<?php

namespace App\Http\Controllers;

use App\Mail\OtpEmail;
use GuzzleHttp\Client;
use App\Repositories\OtpRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    protected OtpRepository $otpRepo;

    public function __construct(OtpRepository $otpRepo)
    {
        $this->otpRepo = $otpRepo;
    }
    
    /**
     *
     * @param Array $phone
     * @return object
     */
    public function create(Array $params)
    {
        return $this->otpRepo->create($params);
    }
    
    /**
     * @param String $params
     */
    public function isExist(String $code_id)
    {
        return $this->otpRepo->isExist($code_id);
    }

    /**
     * @param $id
     */
    public function revoke($id)
    {
        return $this->otpRepo->update($id, ['revoked' => 1]);
    }

    public function confirm(String $code_id, String $otpCode)
    {
        $otp = self::isExist($code_id);
        return $otp ? ($otp->otp_code == $otpCode) : null;
    }

    public function sms($clientName, $phone, $otp)
    {
        try {
            App::setLocale('en-US');
            $api = config('domain.mitake') . "/api/mtk/SmSend?CharsetURL=UTF-8";
            $data = array (
                "username" => "83266693SMS",
                "password" => "112Daex0502mitake",
                "dstaddr" => $phone,
                "smbody" => "$otp is your " . __("client-name.$clientName") . " verification code, and the verification code is valid for one minute."
            );

            return (new Client())->post($api, [
                'form_params' => $data
            ])->getBody()->getContents();

        } catch (\Throwable $th) {
            return response()->json(["message" => "Erroe"], 401);
        }
    }

    public function email($clientName, $email, $otp)
    {
        Mail::to($email)->send(new OtpEmail(__("client-name.$clientName"), $otp));
    }
}
