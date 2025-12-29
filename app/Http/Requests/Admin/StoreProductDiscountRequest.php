<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductDiscountRequest extends FormRequest
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
                    if ($this->discount_type == 'percent' && $value > 90) {
                        $fail('مقدار تخفیف درصدی نمی‌تواند بیشتر از 90٪ باشد.');
                    }
                }
            ],
            'start_date_discount' => 'required|date',
            'end_date_discount' => 'required|date|after_or_equal:start_date',
        ];
    }
}
