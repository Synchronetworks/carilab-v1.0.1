<?php

namespace Modules\Commision\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommisionRequest extends FormRequest
{
   public function rules()
    {
$commisionId = request()->id;
$userType = request()->user_type; 
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('commisions')
                    ->where(function ($query) use ($userType) {
                        return $query->where('user_type', $userType) // Ensure uniqueness per user type
                                    ->whereNull('deleted_at');
                })->ignore($commisionId)
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
            'title.unique' => __('messages.title_unique'),
            'type.required' => __('messages.type_required'),
            'value.numeric' => __('messages.value_numeric'),
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
