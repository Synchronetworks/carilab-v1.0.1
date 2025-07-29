<?php

namespace Modules\Coupon\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function rules()
    {
        $couponId = $this->route('coupon');
    
        return [
    'coupon_code' => [
            'required',
            'string',
            'max:50',
            $couponId && $couponId->id ? 'unique:coupons,coupon_code,' . $couponId->id : 'unique:coupons,coupon_code',
        ],
            'discount_type' => 'required|in:percentage,fixed',
          'discount_value' => [
            'required',
            'numeric',
            'min:1',
            function ($attribute, $value, $fail) {
                if ($this->discount_type === 'percentage' && $value > 100) {
                    $fail(__('messages.dicount_cannot_greater_than_100'));
                }
            },
        ],
            'start_at' => 'required|date|after_or_equal:today',
            'end_at' => 'required|date|after:start_at',
            'total_usage_limit' => 'required|integer|min:1',
            'per_customer_usage_limit' => 'required|integer|min:1|lte:total_usage_limit', // Ensure per-customer limit is not greater than total
            'status' => 'required|boolean',
            'applicability' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'coupon_code.required' => __('messages.coupon_code_required'),
        'coupon_code.unique' => __('messages.coupon_code_exists'),
        'discount_value.required' => __('messages.discount_value_required'),
        'discount_value.numeric' => __('messages.discount_value_numeric'),
        'discount_value.min' => __('messages.discount_value_min'),
        'discount_type.required' => __('messages.discount_type_required'),
        'discount_type.in' => __('messages.discount_type_invalid'),
        'start_at.required' => __('messages.start_date_required'),
        'end_at.required' => __('messages.end_date_required'),
        'end_at.after' => __('messages.end_date_after_start'),
        'total_usage_limit.required' => __('messages.total_usage_required'),
        'per_customer_usage_limit.required' => __('messages.per_customer_required'),
        'per_customer_usage_limit.lte' => __('messages.per_customer_exceed_total'),
        'applicability' => __('messages.applicability_required'),

       
        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
