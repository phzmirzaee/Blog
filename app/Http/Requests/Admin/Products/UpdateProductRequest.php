<?php

namespace App\Http\Requests\Admin\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            "name" => "sometimes|string|min:3|max:50|regex:/^[a-zA-Z\s]+$/u",
            "description" => "sometimes|string|min:10",
            "quantity" => "sometimes|integer|min:1",
            "price" => "sometimes|integer|min:3",
            "image" => "sometimes|image|mimes:jpeg,png,jpg,gif,svg",
        ];
    }
}
