<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfigureGlobalCartDicsountRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalCartDiscountController extends Controller
{
    public function configure(ConfigureGlobalCartDicsountRequest $request):JsonResponse
    {
        $validated= $request->validated();

        Setting::updateOrCreate(
            ['key' => 'cart_discount_value'],
            ['value' => $validated['value']],
        );
        Setting::updateOrCreate(
            ['key' => 'cart_discount_start_date'],
            ['value' => $validated['start_date']],
        );
        Setting::updateOrCreate(
            ['key' => 'cart_discount_end_date'],
            ['value' => $validated['end_date']],
        );
        Setting::updateOrCreate(
            ['key' => 'cart_discount_is_active'],
            ['value' => $validated['is_active']],
        );
        Setting::updateOrCreate(
            ['key' => 'cart_discount_threshold'],
            ['value' => $validated['threshold']],
        );
        return response()->json([
            'message'=>' تخفیف با موفقیت تنظیم شد',
        ],200);
    }
}
