<?php

namespace Modules\Earning\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EarningRequest extends FormRequest
{
   public function rules()
    {

        return [
            'name' => ['required'],
        ];
    }


    public function messages()
    {
        return [
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
