<?php

namespace Modules\Subscriptions\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;


class PlanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
            $rules = [
                'name' => ['required',
                    Rule::unique('plan')->where(function ($query) {
                    return $query->where('name', $this->name)
                                ->whereNull('deleted_at');
                })->ignore($this->route('plan'))],

                'duration' => ['required'],
                'description' => ['required'],
                'duration_value' => ['required', 'numeric', 'min:1'],
                'price' => ['required', 'numeric', 'min:1'],

            ];

            if ($this->isMethod('put')) {
                $rules['level'] = ['required'];
            }

            return $rules;

    }

    public function messages()
    {
        return [
            'name.required' => __('messages.name_required'),
            'level.required' => __('messages.level_required'),
            'duration.required' => __('messages.duration_required'),
            'duration_value.required' => __('messages.duration_value_required'),
            'duration_value.numeric' => __('messages.duration_value_numeric'), // Error message for non-numeric price
            'duration_value.min' => __('messages.duration_value_min'),
            'price.required' => __('messages.price_required'),
            'price.numeric' => __('messages.price_numeric'), // Error message for non-numeric price
            'price.min' => __('messages.price_min'),
           
        ];
    }
}
