<?php

namespace Modules\Payout\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayoutRequest extends FormRequest
{
    public function rules()
    {
        $payment_method = $this->input('payment_method');
        $amount = $this->input('amount');
      
        $rules = [
            'user_id' => 'required|exists:users,id',
            'user_type' => 'nullable',
            'payment_method' => 'required|string|max:20',
            'amount' => 'required|numeric|min:0|max:' . $amount,
        ];


        if ($payment_method === 'bank') {
            $rules['bank'] = 'required|string';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'collector_id.required' => __('messages.collector_id_required'),
            'collector_id.exists' => __('messages.collector_id_exists'),
            'vendor_id.required' => __('messages.vendor_id_required'),
            'vendor_id.exists' => __('messages.vendor_id_exists'),
            'payment_method.required' => __('messages.payment_method_required'),
            'amount.required' => __('messages.amount_required'),
            'amount.numeric' => __('messages.amount_numeric'),
            'amount.min' => __('messages.amount_min'),
            'amount.max' => __('messages.amount_max'),
            'bank.required' => __('messages.bank_required'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
