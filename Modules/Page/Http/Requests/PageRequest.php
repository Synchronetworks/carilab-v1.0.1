<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
   public function rules()
    {

        $pageId = $this->route('page'); // Assuming the route parameter is named 'page'

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pages')->ignore($pageId),
            ],
            'description' => ['required', 'string'],
        ];
    }


    public function messages()
    {
        return [
            'name.required' => __('messages.name_required'),
            'name.string' => __('messages.name_string'),
            'name.max' => __('messages.name_max'),
            'description.required' => __('messages.description_required'),
            'description.string' => __('messages.description_string'),
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
