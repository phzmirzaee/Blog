<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CouponsRequest extends FormRequest
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
            'code' => 'required|string|min:3|max:64',
            'discount_type' => 'required|string|in:fixed,percent',
            'value'=>'required|numeric|min:0',
            'usage_limit'=>'required|numeric|min:0',
            'usage_limit_per_user'=>'required|numeric|min:0',
            'is_active' => 'required|boolean',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',

        ];
    }
}
