<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $table = 'email_verifications';
    public $timestamps = false;

    protected $fillable = ['user_id', 'token', 'created_at'];
}
