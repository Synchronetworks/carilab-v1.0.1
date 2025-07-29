<?php

namespace Modules\Vendor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
{
   public function rules()
    {
        $vendorId = request()->id; 

        $rules = [
            'first_name'       => ['required','string','max:255','regex:/^[A-Za-z\s]+$/'],
            'last_name'        => ['required','string','max:255','regex:/^[A-Za-z\s]+$/'],
            'username'         => 'required|string|max:255|unique:users,username,' . $vendorId,
            'email'            => 'required|email|unique:users,email,' . $vendorId,
            'mobile' => [
                'required',
                'regex:/^[\+\-\d\s]+$/',
                'unique:users,mobile,' . $vendorId,
                'max:15'
            ],
            'password'         => 'nullable|string|min:8|confirmed', // Allow nullable for updates
            'country_id'          => 'required|exists:countries,id',
            'state_id'            => 'required|exists:states,id',
            'city_id'             => 'required|exists:cities,id',
            'tax_id'    => 'required|array|min:1',
            'tax_id.*' => 'exists:taxes,id',
            'profile_image'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'address'          => 'required|string|max:500',
            'gender'           => 'required|in:male,female,other',
            'status'           => 'nullable|boolean',
            'set_as_featured'  => 'nullable|boolean',
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
            'first_name.required'       => __('messages.first_name_required'),
            'last_name.required'        => __('messages.last_name_required'),
            'username.required'         => __('messages.username_required'),
            'username.unique'           => __('messages.username_unique'),
            'email.required'            => __('messages.email_required'),
            'email.email'               => __('messages.email_email'),
            'email.unique'              => __('messages.email_unique'),
            'contact_number.required'   => __('messages.contact_number_required'),
            'contact_number.unique'     => __('messages.contact_number_unique'),
            'contact_number.digits_between' => __('messages.contact_number_digits_between'),
            'password.required'         => __('messages.password_required'),
            'password.confirmed'        => __('messages.password_confirmed'),
            'password.min'              => __('messages.password_min'),
            'commission.required'       => __('messages.commission_required'),
            'commission.numeric'        => __('messages.commission_numeric'),
            'commission.min'            => __('messages.commission_min'),
            'commission_type.required'  => __('messages.commission_type_required'),
            'commission_type.in'        => __('messages.commission_type_in'),
            'country.required'          => __('messages.country_required'),
            'country.exists'            => __('messages.country_exists'),
            'state.required'            => __('messages.state_required'),
            'state.exists'              => __('messages.state_exists'),
            'city.required'             => __('messages.city_required'),
            'city.exists'               => __('messages.city_exists'),
            'tax_id.required'           => __('messages.tax_id_required'),
            'tax_id.array'              => __('messages.tax_id_array'),
            'tax_id.min'                => __('messages.tax_id_min'),
            'tax_id.*.exists'           => __('messages.tax_id_exists'),
            'profile_image.image'       => __('messages.profile_image_image'),
            'profile_image.mimes'       => __('messages.profile_image_mimes'),
            'profile_image.max'         => __('messages.profile_image_max'),
            'profile_image.uploaded'    => __('messages.profile_image_uploaded'),
            'address.required'          => __('messages.address_required'),
            'address.string'            => __('messages.address_string'),
            'address.max'               => __('messages.address_max'),
            'gender.required'           => __('messages.gender_required'),
            'gender.in'                 => __('messages.gender_in'),
            'status.boolean'            => __('messages.status_boolean'),
            'set_as_featured.boolean'   => __('messages.set_as_featured_boolean'),

        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

}
