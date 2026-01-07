<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'address';
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'province',
        'city',
        'postal_code',
        'address_line',
        'plaque',
        'unit'
    ];
}
