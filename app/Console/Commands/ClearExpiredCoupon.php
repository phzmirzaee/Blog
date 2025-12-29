<?php

namespace App\Console\Commands;

use App\Models\Cart;
use Illuminate\Console\Command;

class ClearExpiredCoupon extends Command
{

    protected $signature = 'app:clear-expired-coupon';


    protected $description = 'Remove expired coupons from carts';


    public function handle()
    {
        $expirationTime=now()->subMinute();
        $carts=Cart::whereNotNull('coupon_id')
            ->where('coupon_applied_at', '<', $expirationTime)
            ->get();
        foreach ($carts as $cart) {
            $cart->update([
                'coupon_id' => null,
                'coupon_applied_at' => null,
            ]);
        }
    }
}
