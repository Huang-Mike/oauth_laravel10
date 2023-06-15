<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clients extends Model
{
    use HasFactory;

    protected $table = 'oauth_clients';
    
    protected $keyType = 'string';

    public $incrementing = false;

    protected $dates = [
        'expired_at',
    ];

    protected $fillable = [
        'name',
        'redirect',
        'revoked',
    ];
}
