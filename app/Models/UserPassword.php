<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPassword extends Model
{
    use HasFactory;

    protected $table = 'user_passwords';
    
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'password',
        'otp'
    ];
}
