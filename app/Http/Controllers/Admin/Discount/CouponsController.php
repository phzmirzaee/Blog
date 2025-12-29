<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddCouponsRequest;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    public function addCoupon(AddCouponsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        Coupon::updateOrCreate(
            ['code' => $validated['code']],
            [
                'discount_type' => $validated['discount_type'],
                'is_active' => $validated['is_active'],
                'value' => $validated['value'],
                'usage_limit' => $validated['usage_limit'],
                'usage_limit_per_user' => $validated['usage_limit_per_user'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]
        );
        return response()->json([
            'message' => 'کد تخفیف شمااضافه شد.',
        ],200);
    }
}
