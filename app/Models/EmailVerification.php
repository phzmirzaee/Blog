<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailVerification extends Model
{
    protected $table = 'email_verifications';
    public $timestamps = false;

    protected $fillable = ['user_id', 'token', 'created_at'];

    public function User():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
