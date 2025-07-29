<?php

namespace Modules\Tax\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaxRequest extends FormRequest
{
    public function rules()
    {

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('taxes', 'title')->ignore($this->route('tax'))
            ],
            'type' => 'required|in:Fixed,Percentage',
            'value' => ['required','numeric','min:0',  
                function($attribute, $value, $fail) {
                    if ($this->type == 'Percentage' && ($value < 1 || $value > 100)) {
                        $fail('The ' . $attribute . ' Value must be between 1 and 100 for Percentage.');
                    }
                }
            ], 

        ];
    }


    public function messages()
    {
        return [
            'title.required' => __('messages.title_required'),
            'type.required' => __('messages.type_required'),
            'value.required' => __('messages.value_required'),
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
