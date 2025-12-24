<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $table = 'carts';
    protected $fillable=['user_id','coupon_id','coupon_applied_at'];

    public function items():hasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function coupon():belongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
