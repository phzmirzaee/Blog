<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForgotPassword extends Model
{
    public $fillable = ['email', 'token', 'expired_at'];
    protected $table = 'forgot_password';
    public $timestamps = false;
}
