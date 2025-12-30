<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class AddProductDiscountRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'discount_type' => 'required|in:fixed,percent',
            'is_active_discount' => 'required|boolean',
            'discount_value' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value <= 0) {
                        $fail('مقدار تخفیف نمیتواند صفر یا منفی باشد.');
                    }
                    if ($this->discount_type == 'percent' && $value > 90) {
                        $fail('مقدار تخفیف درصدی نمی‌تواند بیشتر از 90٪ باشد.');
                    }
                    if($this->discount_type=='fixed'){
                        $product=Product::findOrFail($this->id);
                        if($product&&$value>$product->price) {
                            $fail('مقدار تخفیف نمی تواند بیشتر از قیمت محصول باشد.');
                        }
                    }
                }
            ],
            'start_date_discount' => 'required|date',
            'end_date_discount' => 'required|date|after_or_equal:start_date_discount',
        ];
    }
}
