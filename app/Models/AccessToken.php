<?php

namespace App\Models;

use App\Models\Model;
use App\Models\RefreshToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessToken extends Model
{
    use HasFactory;

    protected $table = 'oauth_access_tokens';
    
    protected $keyType = 'string';

    public $incrementing = false;

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $dates = [
        'expired_at',
    ];

    protected $fillable = [
        'revoked'
    ];

    public function refreshToken()
    {
        return $this->hasOne(RefreshToken::class);
    }
}
