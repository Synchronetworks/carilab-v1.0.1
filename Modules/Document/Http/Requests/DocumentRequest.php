<?php

namespace Modules\Document\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
   public function rules()
    {

        $documentId = $this->route('document'); // Get the document ID from route if it exists
    
        return [
            'name' => [
                'required',
                \Illuminate\Validation\Rule::unique('documents', 'name')
                    ->where('user_type', $this->user_type)
                    ->ignore($documentId)
            ],
            'user_type' => ['required']
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
