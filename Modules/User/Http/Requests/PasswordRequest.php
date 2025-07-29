<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class PasswordRequest extends FormRequest
{
   public function rules()
    {
        {
            $rules = [
                'old_password' => ['required'],
                'password' => ['required', 'min:8', 'confirmed']
            ];
    
            return $rules;
        }
    }


    public function messages()
    {
        return [
       
            'password.required' => __('messages.password_required'),
            'password.min' => __('messages.password_min'),
            'password.confirmed' => __('messages.password_confirmed'),
            'old_password.required' => __('messages.old_password_required'),
         
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
