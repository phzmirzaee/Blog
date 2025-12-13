<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductsDiscountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer',
            'discount_type' => 'required|in:fixed,percent',
            'is_active'=>'required|boolean',
            'value'=>'required|numeric|min:1',
            'start_date'=>'required|date',
            'end_date'=>'required|date|after_or_equal:start_date',


        ];
    }
}
