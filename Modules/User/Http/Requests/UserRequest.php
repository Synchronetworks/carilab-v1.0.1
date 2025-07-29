<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UserRequest extends FormRequest
{
   public function rules()
    {
        {
            $userId = request()->id;
            $rules = [
                'first_name' => ['required','string','max:255','regex:/^[A-Za-z\s]+$/'],
                'last_name' => ['required','string','max:255','regex:/^[A-Za-z\s]+$/'],
                'username'  => 'required|string|max:255|unique:users,username,' . $userId,
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($this->route('user'))
                ],
                'mobile' => [
                    'required',
                    'regex:/^[\+\-\d\s]+$/',
                    Rule::unique('users')->ignore($this->route('user')),
                    'min:10',
                    'max:15'
                ],
                'gender' => ['required', 'in:male,female,other'],
                'date_of_birth' => ['required']
            ];


            if ($this->isMethod('post')) {
                $rules['password'] = ['required', 'min:8', 'confirmed'];
            }
    
            return $rules;
        }
    }


    public function messages()
    {
        return [
            'first_name.required' => __('messages.first_name_required'),
            'last_name.required' => __('messages.last_name_required'),
            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.email_valid'),
            'email.unique' => __('messages.email_unique'),
            'password.required' => __('messages.password_required'),
            'password.min' => __('messages.password_min'),
            'password.confirmed' => __('messages.password_confirmed'),
            'gender.required' => __('messages.gender_required'),
            'mobile.required' => __('messages.mobile_required'),
            'gender.in' => __('messages.gender_in'),
            'date_of_birth.required' => __('messages.date_of_birth_required'),
            'first_name.regex' => __('messages.first_name_regex'),
            'last_name.regex' => __('messages.last_name_regex'),
            'profile_image.image' => __('messages.profile_image_image'),
            'profile_image.mimes' => __('messages.profile_image_mimes'),
            'profile_image.max' => __('messages.profile_image_max'),
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
