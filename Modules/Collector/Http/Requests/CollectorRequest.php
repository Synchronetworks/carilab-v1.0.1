<?php

namespace Modules\Collector\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class CollectorRequest extends FormRequest
{
   public function rules()
    {
        
       
        $collectorId = request()->id; 
        $rules = [
            'first_name' => 'required|string|max:255|regex:/^[A-Za-z\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[A-Za-z\s]+$/',
            'email' => 'required|email|unique:users,email,' . $collectorId,
            'username' => 'required|string|unique:users,username,' . $collectorId,
            'mobile' => 'required|regex:/^[\+\-\d\s]+$/|max:15|unique:users,mobile,' . $collectorId,
            'password'         => 'nullable|string|min:8|confirmed',
            'vendor_id' => 'nullable|exists:users,id',
            'lab_id' => 'required|exists:labs,id',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'address' => 'nullable|string',
            'education' => 'nullable|string|max:255',
            'degree' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'experience' => 'nullable|string|max:255',
            'status' => 'boolean',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
        if($this->has('commission_type') && $this->has('commission')) {
            $rules['commission'] = 'required|numeric|min:0';
            $rules['commission_type']  = 'required|in:fixed,percentage';
        } 
        return $rules;
    }


    public function messages()
    {
        return [
            'first_name.required' => __('messages.first_name_required'),
            'last_name.required' => __('messages.last_name_required'),
            'email.required' => __('messages.email_required'),
            'username.required' => __('messages.username_required'),
            'mobile.required' => __('messages.mobile_required'),
            'vendor_id.required' => __('messages.vendor_required'),
            'lab_id.required' => __('messages.lab_required'),
            'country_id.required' => __('messages.country_required'),
            'state_id.required' => __('messages.state_required'),
            'city_id.required' => __('messages.city_required'),
            'address.required' => __('messages.address_required'),
            'education.required' => __('messages.education_required'),
            'degree.required' => __('messages.degree_required'),
            'bio.required' => __('messages.bio_required'),
            'commission.required' => __('messages.commission_required'),
            'commission.numeric' => __('messages.commission_numeric'),
            'commission.min' => __('messages.commission_min'),
            'commission_type.required' => __('messages.commission_type_required'),
            'commission_type.in' => __('messages.commission_type_in'),
            'profile_image.required' => __('messages.profile_image_required'),
            'profile_image.image' => __('messages.profile_image_image'),
            'profile_image.mimes' => __('messages.profile_image_mimes'),
            'profile_image.max' => __('messages.profile_image_max'),
            'profile_image.uploaded' => __('messages.profile_image_uploaded'),
            'status.required' => __('messages.status_required'),
            'email.unique' => __('messages.email_unique'),
            'username.unique' => __('messages.username_unique'),
            'mobile.unique' => __('messages.mobile_unique'),
            'vendor_id.exists' => __('messages.vendor_exists'),
            'lab_id.exists' => __('messages.lab_exists'),
            'country_id.exists' => __('messages.country_exists'),
            'state_id.exists' => __('messages.state_exists'),
            'city_id.exists' => __('messages.city_exists'),
            'password.required' => __('messages.password_required'),
            'password.confirmed' => __('messages.password_confirmed'),
            'password.min' => __('messages.password_min'),
            'first_name.regex' => __('messages.first_name_regex'),
            'last_name.regex' => __('messages.last_name_regex'),
        ];
    }
   
}
