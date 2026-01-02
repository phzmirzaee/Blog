<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AddCouponsRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'code' => 'required|string|min:3|max:64',
            'discount_type' => 'required|string|in:fixed,percent',
            'value' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value <= 0) {
                        $fail('مقدار تخفیف نمیتواند صفر یا منفی باشد.');
                    }
                    if ($this->discount_type == 'percent' && $value > 90) {
                        $fail('مقدار تخفیف درصدی نمی‌تواند بیشتر از 90٪ باشد.');
                    }

                }
            ],
            'usage_limit' => 'required|numeric|min:0',
            'usage_limit_per_user' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',

        ];
    }
}
