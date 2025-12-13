<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    protected $table = 'products_discount';
    protected $fillable = [
        'product_id',
        'discount_type',
        'is_active',
        'value',
        'start_date',
        'end_date',
    ];
    public $timestamps = false;
}
