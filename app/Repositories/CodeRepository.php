<?php

namespace App\Repositories;

use App\Models\Code;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class CodeRepository
{
    /**
     * Create a code valid for 10 minutes.
     *
     * @param String $client_id
     * @return void
     */
    public function create(String $client_id)
    {
        $params['id'] = Str::uuid()->toString();
        $params['client_id'] = $client_id;
        $params['expired_at'] = Carbon::now()->addMinutes(10)->timestamp;
        return Code::create($params);
    }

    /**
     * Update
     *
     * @param String $code_id
     * @param Array $params
     * @return void
     */
    public function update(String $code_id, Array $params)
    {
        $code = Code::find($code_id);
        return $code->update($params);
    }

    public function isAvailable(String $code_id)
    {
        return Code::where('id', $code_id)->where('revoked', 0)->where('expired_at', '>', time())->first();
    }

    public function find(String $code_id)
    {
        return Code::find($code_id);
    }
}
