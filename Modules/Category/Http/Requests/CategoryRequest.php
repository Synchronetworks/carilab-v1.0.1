<?php

namespace Modules\Category\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'category_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|boolean'
        ];

        // If this is an update request
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $categoryId = request()->id;
            $rules['name'] = 'required|max:255|unique:categories,name,'.$categoryId;
        }

        return $rules;
    }


    public function messages()
    {
        return [
            'name.required' => __('messages.category_name_required'),
            'name.unique' => __('messages.category_name_unique'),
            'category_image.image' => __('messages.category_image_type'),
            'category_image.mimes' => __('messages.category_image_mimes'),
            'category_image.max' => __('messages.category_image_max'),
            'category_image.uploaded' => __('messages.category_image_uploaded'),
            'status.required' => __('messages.status_required'),
            'status.boolean' => __('messages.status_boolean'),

        ];
    }
  
    
}

