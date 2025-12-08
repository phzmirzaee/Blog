<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function configure(SettingRequest $request):JsonResponse
    {
        $validated= $request->validated();

        Setting::updateOrCreate(
            ['key' => 'basket_discount_value'],
            ['value' => $validated['value']],
        );
        Setting::updateOrCreate(
            ['key' => 'basket_discount_start_date'],
            ['value' => $validated['start_date']],
        );
        Setting::updateOrCreate(
            ['key' => 'basket_discount_end_date'],
            ['value' => $validated['end_date']],
        );
        Setting::updateOrCreate(
            ['key' => 'basket_discount_is_active'],
            ['value' => $validated['is_active']],
        );
        Setting::updateOrCreate(
            ['key' => 'basket_discount_threshold'],
            ['value' => $validated['threshold']],
        );
        return response()->json([
            'message'=>'تنظیمات تخفیف با موفقیت ذخیره شد',
        ],200);
    }
}
