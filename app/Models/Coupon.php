<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';
    public $timestamps = false;
    protected $fillable = [
        'code',
        'discount_type',
        'is_active',
        'value',
        'usage_limit',
        'usage_limit_per_user',
        'start_date',
        'end_date',
    ];
}
