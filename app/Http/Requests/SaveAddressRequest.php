<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveAddressRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'full_name' => 'required|string|regex:/^[\p{L}]+(?:\s+[\p{L}]+)+$/u',
            'phone' => 'required|regex:/^09\d{9}$/',
            'province' => 'required|regex:/^[\p{L}\s]+$/u',
            'city' => 'required|regex:/^[\p{L}\s]+$/u',
            'postal_code' => 'required|digits:10',
            'address_line' => 'required|regex:/^[\p{L}0-9\s\-\/]+$/u',
            'plaque' => 'required|regex:/^[0-9]+$/',
            'unit' => 'required|regex:/^[0-9]+$/',
        ];
    }
}
