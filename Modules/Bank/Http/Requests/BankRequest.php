<?php

namespace Modules\Bank\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class BankRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'bank_name' => ['required', 'string', 'max:191'],
            'branch_name' => ['required', 'string', 'max:191'],
            'ifsc_code' => ['required', 'string', 'max:191'],
            'account_no' => ['required', 'string', 'max:191'],
            'phone_number' => [
                'required',
                'regex:/^[\+\-\d\s]+$/',
                'min:10',
                'max:15'
            ],
            'user_id' => ['required', 'exists:users,id'],
            'status' => ['boolean'],
        ];

        // Add composite unique validation
        $bankId = $this->route('bank') ?? $this->id; // Get ID for update requests

        if ($bankId) {
            
            $rules['account_no'][] = Rule::unique('banks')->ignore($bankId, 'id');
        } else {
           
            $rules['account_no'][] = Rule::unique('banks');
               
        }
            
        return $rules;
    }

    public function messages()
    {
        return [
            'account_no.unique' => __('messages.account_no_unique'),
            'bank_name.required' => __('messages.bank_name_required'),
            'branch_name.required' => __('messages.branch_name_required'),
            'account_no.required' => __('messages.account_no_required'),
            'phone_number.required' => __('messages.phone_number_required'),
            'phone_number.regex' => __('messages.phone_number_invalid'),
            'status.required' => __('messages.status_required'),
            'user_id.required' => __('messages.user_required'),
            'user_id.exists' => __('messages.user_exists'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $data = [
            'status' => false,
            'message' => $validator->errors()->first(),
            'all_message' => $validator->errors(),
        ];

        if (request()->wantsJson() || request()->is('api/*')) {
            throw new HttpResponseException(response()->json($data, 422));
        }

        throw new HttpResponseException(
            redirect()
                ->back()
                ->withErrors($validator) 
                ->withInput()
        );
    }
}
