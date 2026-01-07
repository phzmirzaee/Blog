<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfigureShippingRequest;
use App\Models\ProductDiscount;
use App\Models\shipping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function configure(ConfigureShippingRequest $request):JsonResponse
    {
        $validated = $request->validated();
        Shipping::updateOrCreate(
            ['title' => $validated['title']],
            [
                'description' => $validated['description'],
                'price' => $validated['price']
            ]
        );
        return response()->json([
            'message' => 'با موفقیت ذخیره شد.'
        ]);
    }
}
