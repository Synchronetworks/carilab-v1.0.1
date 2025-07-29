<?php

namespace Modules\World\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
{
   public function rules()
    {

        return [
            'state_id' => 'required',
            'name' => ['required'],
        ];
    }


    public function messages()
    {
        return [
            'state_id.required' => __('messages.state_id_required'),
            'name.required' => __('messages.name_required'),
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
