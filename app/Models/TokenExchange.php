<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TokenExchange extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    protected $table = 'token_exchange';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'grant',
        'tokens'
    ];
}
