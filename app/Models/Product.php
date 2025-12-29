<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    protected $table = 'product';
    protected $fillable = [
        'name',
        'description',
        'quantity',
        'price',
        'image',
        'discount_type',
        'is_active_discount',
        'discount_value',
        'start_date_discount',
        'end_date_discount',
    ];
}
