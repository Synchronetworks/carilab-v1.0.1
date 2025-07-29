<?php

namespace Modules\Appointment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
   public function rules()
    {

        $rules = [
            'customer_id' => 'required|exists:users,id',
            'vendor_id' => 'nullable|exists:users,id',
            'lab_id' => 'required|exists:labs,id',
            'test_type' => 'required|in:test_case,test_package',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'other_members' => 'nullable',
            'symptoms' => 'nullable',
            'medical_report' => 'nullable',
            'collection_type' => 'required|in:home,lab',
        ];
        if($this->test_type == 'test_case'){
            $rules['test_id'] = 'required|exists:catlogmanagements,id';
        }else{
            $rules['test_id'] = 'required|exists:packagemanagements,id';
        }
        return $rules;
    }


    public function messages()
    {
        return [
            'customer_id.required' => __('messages.customer_required'),
            'customer_id.exists' => __('messages.customer_not_exists'),
            'vendor_id.exists' => __('messages.vendor_not_exists'),
            'lab_id.required' => __('messages.lab_required'),
            'lab_id.exists' => __('messages.lab_not_exists'),
            'test_type.required' => __('messages.test_type_required'),
            'test_type.in' => __('messages.test_type_invalid'),
            'test_id.required_if' => __('messages.test_required'),
            'test_id.exists' => __('messages.test_not_exists'),
            'appointment_date.required' => __('messages.appointment_date_required'),
            'appointment_date.date' => __('messages.appointment_date_invalid'),
            'appointment_time.required' => __('messages.appointment_time_required'),
            'appointment_time.date_format' => __('messages.appointment_time_invalid'),
            'collection_type.required' => __('messages.collection_type_required'),
            'collection_type.in' => __('messages.collection_type_invalid'),
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
