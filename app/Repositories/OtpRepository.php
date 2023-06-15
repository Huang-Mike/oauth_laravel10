<?php

namespace App\Repositories;

use App\Models\Otp;
use Illuminate\Support\Carbon;

class OtpRepository
{
    /**
     * Create a otp code valid for 3 minutes.
     *
     * @param Array $params
     * @return Object
     */
    public function create(Array $params)
    {
        $params['otp_code'] = random_int(100000, 999999);
        $params['expired_at'] = Carbon::now()->addMinutes(1)->timestamp;

        return Otp::create($params);
    }

    public function update($id, Array $params)
    {
        return Otp::find($id)->update($params);
    }

    /**
     * @param String $params
     */
    public function isExist(String $code_id)
    {
        $query = Otp::where('code_id', $code_id)
                    ->where('revoked', 0)
                    ->where('expired_at', '>', time());

        if ($query->exists()) {
            $otp = $query->first();
        }

        return $otp ?? null;
    }
}
