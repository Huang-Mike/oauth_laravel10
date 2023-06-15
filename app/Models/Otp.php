<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Otp extends Model
{
    use HasFactory;

    protected $table = 'otps';

    protected $dates = [
        'expired_at',
    ];

    protected $fillable = [
        'code_id',
        'primary_key',
        'otp_code',
        'revoked',
        'expired_at',
    ];

}
