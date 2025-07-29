<?php

namespace Modules\Lab\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LabRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return true;
    // }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
       
        $labId = request()->id;
        $rules = [
            // Basic Information
            'name' => 'required|string|max:191|unique:labs,name,'.$labId,
            'lab_code' => 'nullable|string|max:191',
            'description' => 'nullable|string',
            'vendor_id' => 'required|exists:users,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Contact Information
            'phone_number' => 'required|string|max:20|unique:labs,phone_number,'.$labId,
            'email' => 'required|email|max:191|unique:labs,email,'.$labId,
            
            // Address Information
            'address_line_1' => 'required|string|max:191',
            'address_line_2' => 'nullable|string|max:191',
            'city_id' => 'required|exists:cities,id',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id',
            'postal_code' => 'required|string|max:20',
            
            // Geo Location
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'geo_location' => 'nullable|string',
            
            // Working Hours
            // 'working_hours_start' => 'required|date_format:H:i',
            // 'working_hours_end' => 'required|date_format:H:i|after:working_hours_start',
            'time_slot' => 'required|integer',
            
            // License Information
            'license_number' => 'required|string|max:191',
            'license_expiry_date' => 'required|date|after:today',
            
            // Accreditation Information
            'accreditation_type' => 'nullable|string|in:NABL,ISO',
            'accreditation_expiry_date' => 'nullable|date|after:today',
            'accreditation_certificate' => 'nullable|file|max:2048',
            
            // // Tax and Payment Information
            'tax_identification_number' => 'required|string|max:191',
            'payment_modes' => 'required|array',
            'payment_modes.*' => 'in:manual,online',
            // 'payment_gateways' => 'required_if:payment_modes.*,online|array',
            // 'payment_gateways.*' => 'exists:settings,id',
            
            // // Status
            'status' => 'required|boolean',
        ];
        if ($this->isMethod('post') || $this->hasFile('license_document')) {
            $rules['license_document'] = 'required|file|max:2048';
        } else {
            $rules['license_document'] = 'nullable|file|max:2048';  // Optional for editing
        }
        return $rules;
    }

    public function messages()
    {
        
        $messages = [
            'name.required' => __('messages.lab_name_required'),
            'name.max' => __('messages.lab_name_max'),
            'vendor_id.required' => __('messages.vendor_id_required'),
            'vendor_id.exists' => __('messages.vendor_id_exists'),
            'logo.image' => __('messages.logo_image'),
            'logo.mimes' => __('messages.logo_mimes'),
            'logo.max' => __('messages.logo_max'),
            'logo.uploaded' => __('messages.logo_uploaded'),

            'phone_number.required' => __('messages.phone_number_required'),
            'phone_number.max' => __('messages.phone_number_max'),
            'phone_number.unique' => __('messages.phone_number_unique'),

            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.email_valid'),
            'email.unique' => __('messages.email_unique'),
            
            'address_line_1.required' => __('messages.address_line_1_required'),
            'city_id.required' => __('messages.city_id_required'),
            'state_id.required' => __('messages.state_id_required'),
            'country_id.required' => __('messages.country_id_required'),
            'postal_code.required' => __('messages.postal_code_required'),
            
            // 'working_hours_start.required' => 'Working hours start time is required',
            // 'working_hours_end.required' => 'Working hours end time is required',
            // 'working_hours_end.after' => 'End time must be after start time',
           'time_slot.required' => __('messages.time_slot_required'),
            
            'license_number.required' => __('messages.license_number_required'),
            'license_document.mimes' => __('messages.license_document_mimes'),
            'license_document.max' => __('messages.license_document_max'),
            'license_document.uploaded' => __('messages.license_document_uploaded'),
            'license_expiry_date.required' => __('messages.license_expiry_date_required'),
            'license_expiry_date.after' => __('messages.license_expiry_date_after'),
            
            'accreditation_certificate.mimes' => __('messages.accreditation_certificate_mimes'),
            'accreditation_certificate.max' => __('messages.accreditation_certificate_max'),
            'accreditation_expiry_date.after' => __('messages.accreditation_expiry_date_after'),
            'accreditation_certificate.uploaded' => __('messages.accreditation_certificate_uploaded'),
            'tax_identification_number.required' => __('messages.tax_identification_number_required'),
            'tax_identification_number.max' => __('messages.tax_identification_number_max'),
            'payment_modes.*.in' => __('messages.payment_modes_in'),
            'payment_gateways.required_if' => __('messages.payment_gateways_required_if'),
                        
            'latitude.between' => __('messages.latitude_between'),
            'longitude.between' => __('messages.longitude_between'),
        ];
        return $messages;
    }
}
