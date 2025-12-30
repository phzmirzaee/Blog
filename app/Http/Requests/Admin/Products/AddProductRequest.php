<?php

namespace App\Http\Requests\Admin\Products;

use Illuminate\Foundation\Http\FormRequest;

class AddProductRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            "name" => "required|string|min:3|max:50|regex:/^[a-zA-Z\s]+$/u",
            "description" => "required|string|min:10",
            "quantity" => "required|integer|min:1",
            "price" => "required|integer|min:3",
            "image" => "required|image|mimes:jpeg,png,jpg,gif,svg",
        ];
    }
}
