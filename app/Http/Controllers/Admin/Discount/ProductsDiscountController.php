<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductsDiscountRequest;
use App\Models\ProductDiscount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductsDiscountController extends Controller
{
    public function configure(ProductsDiscountRequest $request): JsonResponse
    {
        $validated = $request->validated();
        ProductDiscount::updateOrCreate(
            ['product_id' => $validated['product_id']],
            [
                'discount_type' => $validated['discount_type'],
                'is_active' => $validated['is_active'],
                'value' => $validated['value'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]
        );
        return response()->json([
            'message' => 'تخفیف محصولات با موفقیت ذخیره شد.',
        ]);
    }
}
