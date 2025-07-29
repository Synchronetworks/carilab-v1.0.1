<?php

namespace Modules\Currency\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Adjust as per your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'currency_name' => ['required', 'string', 'max:255'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'currency_code' => ['required', 'string', 'max:5'],
            'is_primary' => ['nullable', 'boolean'],
            'currency_position' => ['required', 'string', 'in:left,right,left_with_space,right_with_space'],
            'thousand_separator' => ['required', 'string', 'max:1'],
            'decimal_separator' => ['required', 'string', 'max:1'],
            'no_of_decimal' => ['required', 'integer'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'currency_name.required' => __('messages.currency_name_required'),
            'currency_symbol.required' => __('messages.currency_symbol_required'),
            'currency_code.required' => __('messages.currency_code_required'),
            'currency_position.required' => __('messages.currency_position_required'),
            'currency_position.in' => __('messages.currency_position_in'),
            'thousand_separator.required' => __('messages.thousand_separator_required'),
            'decimal_separator.required' => __('messages.decimal_separator_required'),
            'no_of_decimal.required' => __('messages.no_of_decimal_required'),
            'no_of_decimal.integer' => __('messages.no_of_decimal_integer'),
        ];
    }
}
