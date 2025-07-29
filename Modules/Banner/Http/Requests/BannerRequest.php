<?php

namespace Modules\Banner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
   public function rules()
    {

        $rules = [
            'name' => 'required|max:255|unique:banners,name',
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|boolean'
        ];

        // If this is an update request
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $bannerId = request()->id;
            $rules['name'] = 'required|max:255|unique:banners,name,'.$bannerId;
        }

        return $rules;
    }


    public function messages()
    {
        return [
            'name.required' => __('messages.banner_name_required'),
            'name.unique' => __('messages.banner_name_unique'),
            'Banner_image.image' => __('messages.banner_image_type'),
            'Banner_image.mimes' => __('messages.banner_image_mimes'),
            'Banner_image.max' => __('messages.banner_image_max'),
            'Banner_image.uploaded' => __('messages.banner_image_uploaded'),
            'status.required' => __('messages.status_required'),
            'status.boolean' => __('messages.status_boolean'),

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
