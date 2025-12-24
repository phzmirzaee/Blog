<?php

namespace App\Console\Commands;

use App\Models\Cart;
use Illuminate\Console\Command;

class ClearExpiredCoupon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-expired-coupon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
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
