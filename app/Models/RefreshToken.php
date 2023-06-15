<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RefreshToken extends Model
{
    use HasFactory;

    protected $table = 'oauth_refresh_tokens';
    
    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $dates = [
        'expired_at',
    ];

    protected $fillable = [
        'revoked'
    ];

    public function accessToken()
    {
        return $this->hasOne(AccessToken::class);
    }
}
