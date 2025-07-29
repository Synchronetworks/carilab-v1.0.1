<?php

namespace Modules\PackageManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackageManagementRequest extends FormRequest
{
    public function rules()
    {
        $packageID = request()->id;
        $rules = [
            // Required fields
            'name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('packagemanagements')->where(function ($query) {
                    return $query->where('lab_id', request()->lab_id);
                })->ignore($packageID), // Ignore current record when updating
            ],

            'description' => 'nullable|string',
            'catalog_id' => 'required|array',
            'catalog_id.*' => 'exists:catlogmanagements,id',
            'vendor_id' => 'required|exists:users,id',
            'lab_id' => 'required|exists:labs,id',
            'price' => 'required|numeric',
            'start_at' => 'required|date|after_or_equal:today',
            'end_at' => 'required|date|after:start_at',


            // Boolean fields (with default)
            'status' => 'boolean',
            'is_featured' => 'boolean',



            // Discount related
            'is_discount' => 'boolean',
            'discount_type' => 'nullable|string|max:191',
            'discount_price' => [
                'nullable',
                'numeric',
                Rule::when(request()->discount_type === 'percentage', ['min:1', 'max:100']),
                
            ],

            // File uploads
            'package_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ];

        return $rules;
    }
    protected function prepareForValidation()
    {
        // Convert empty strings to null for nullable fields
        $this->merge([
            'description' => $this->description ?: null,

            'status' => $this->has('status') ? (int) $this->boolean('status') : 1, // Default 1
            'is_featured' => (int) $this->boolean('is_featured'), // Default 0
            'is_discount' => (int) $this->boolean('is_discount'), // Default 0
            'discount_type' => $this->discount_type ?: null,
            'discount_price' => $this->discount_price ?: 0,

        ]);
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $discountType = $this->input('discount_type');
            $discountPrice = $this->input('discount_price');
            $price = $this->input('price');

            if ($discountType === 'fixed' && $discountPrice > $price) {
                $validator->errors()->add('discount_price', __('messages.if_discount_type_is_fixed_discount_price_cannot_exceed_package_price'));
            }
        });
    }   

    public function messages()
    {
        return [
            // Required validations
            'name.required' => __('messages.package_name_required'),
            'name.unique' => __('messages.package_name_unique'),
            'lab_id.required' => __('messages.lab_required'),
            'vendor_id.required' => __('messages.vendor_required'),
            'price.required' => __('messages.price_required'),
            'start_at.required' => __('messages.start_at_required'),
            'end_at.required' => __('messages.end_at_required'),
            'status.required' => __('messages.status_required'),
            'is_featured.required' => __('messages.is_featured_required'),
            'catalog_id.required' => __('messages.catalog_required'),
            'discount_price.required' => __('messages.discount_price_required'),
            'discount_type.required' => __('messages.discount_type_required'),

            // Exists validations
            'category_id.exists' => __('messages.category_invalid'),
            'lab_id.exists' => __('messages.lab_invalid'),
            'vendor_id.exists' => __('messages.vendor_invalid'),

            // Image validations
            'package_image.image' => __('messages.package_image_image'),
            'package_image.mimes' => __('messages.package_image_mimes'),
            'package_image.max' => __('messages.package_image_max'),
            'package_image.uploaded' => __('messages.package_image_uploaded'),

            //discount validations
            'discount_type.string' => __('messages.discount_type_string'),
            'discount_price.numeric' => __('messages.discount_price_numeric'),

            // String validations with max length
            'name.max' => __('messages.name_max'),

            // String type validations
            'description.string' => __('messages.description_string'),


            // Boolean validations
            'is_discount.boolean' => __('messages.is_discount_boolean'),
            'status.boolean' => __('messages.status_boolean'),
            'is_featured.boolean' => __('messages.is_featured_boolean'),

            // Numeric validations
            'price.numeric' => __('messages.price_numeric'),
            'start_at.date' => __('messages.start_at_date'),
            'end_at.date' => __('messages.end_at_date'),
            'start_at.before_or_equal' => __('messages.start_at_before_or_equal'),
            'end_at.after_or_equal' => __('messages.end_at_after_or_equal'),

            'discount_price.min' => __('messages.discount_price_min'),
            'discount_price.max' => __('messages.discount_price_max'),

            // Equipment validations

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
