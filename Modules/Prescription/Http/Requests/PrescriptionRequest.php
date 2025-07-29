<?php

namespace Modules\Prescription\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PrescriptionRequest extends FormRequest
{
   public function rules()
    {

        return [
            'user_id' => 'required|exists:users,id',
            'lab_id' => 'nullable|exists:labs,id',
            'catlog_id' => 'nullable|exists:catlogmanagements,id',
            'package_id' => 'nullable|exists:packagemanagements,id',
            'uploaded_at' => 'nullable|date',
            'prescription_status' => 'default:0|boolean',
            'status' => 'default:1|boolean',
            'prescription_upload' => [
                'required',
                'file',
                'max:5120'
            ],
        ];
    }


    public function messages()
    {
        return [
            'user_id.required' => __('messages.user_required'),
            'lab_id.required' => __('messages.lab_required'),
            'catlog_id.required' => __('messages.catlog_required'),
            'package_id.required' => __('messages.package_required'),
            'uploaded_at.required' => __('messages.uploaded_at_required'),
            'prescription_status.required' => __('messages.prescription_status_required'),
            'prescription_status.boolean' => __('messages.prescription_status_boolean'),
            'status.required' => __('messages.status_required'),
            'status.boolean' => __('messages.status_boolean'),
            'prescription_upload.required' => __('messages.prescription_upload_required'),
            'prescription_upload.image' => __('messages.prescription_upload_image'),
            'prescription_upload.mimes' => __('messages.prescription_upload_mimes'),
            'prescription_upload.max' => __('messages.prescription_upload_max'),
            'uploaded_at.date' => __('messages.uploaded_at_date'),
            'prescription_upload.uploaded' => __('messages.prescription_upload_uploaded'),
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
    protected function failedValidation(Validator $validator)
    {
        if ( request()->is('api*')){
            $data = [
                'status' => 'false',
                'message' => $validator->errors()->first(),
                'all_message' =>  $validator->errors()
            ];

            throw new HttpResponseException(response()->json($data,422));
        }

        throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
    }
}
