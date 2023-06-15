<?php

namespace App\Models;

use App\Models\Otp;
use App\Models\Clients;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Code extends Model
{
    use HasFactory;

    protected $table = 'codes';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $dateFormat = 'U';

    protected $dates = [
        'expired_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'client_id',
        'revoked',
        'expired_at',
    ];

    public function client()
    {
        return $this->belongsTo(Clients::class);
    }

    public function otp()
    {
        return $this->hasOne(Otp::class)->where('revoked', 0)->where('expired_at', '>', time());
    }
}
