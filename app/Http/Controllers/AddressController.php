<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAddressRequest;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function saveAddress(SaveAddressRequest $request): JsonResponse
    {
        $validated = $request->validated();
        Address::updateOrCreate(
            ['user_id' => auth()->id(),],
            ['full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'province' => $validated['province'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'address_line' => $validated['address_line'],
                'plaque' => $validated['plaque'],
                'unit' => $validated['unit'],

            ]);
        return response()->json([
            'message' => 'آدرس شما با موفقیت ذخیره شد'
        ], 200);
    }
}
