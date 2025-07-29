<?php

namespace Modules\CatlogManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatlogManagementRequest extends FormRequest
{
   public function rules()
    {
        $testcaseId = request()->id;
        $rules =  [
            // Required fields
            'name' => [
            'required',
            'string',
            'max:191',
            Rule::unique('catlogmanagements')->where(function ($query) {
                return $query->where('lab_id', request()->lab_id);
            })->ignore($testcaseId), // Ignore current record when updating
        ],
            'code' => 'required|string|max:191',
            'type' => 'required|array',
            'type.*' => 'string|max:191',
            'equipment' => 'required|array',
            'equipment.*' => 'string|max:191',
            'category_id' => 'required|exists:categories,id',
            'vendor_id' => 'required|exists:users,id',
            'lab_id' => 'required|exists:labs,id',
            'price' => 'required|numeric',
            'duration' => 'required|date_format:H:i',
            'test_report_time' => 'required|date_format:H:i',
            
            // Boolean fields (with default)
            'status' => 'boolean',
            'is_home_collection_available' => 'boolean',
            'is_featured' => 'boolean',
            
            // Nullable fields
            'description' => 'nullable|string|max:191',
            'instructions' => 'nullable|string',
            'restrictions' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            
            // File uploads
            'test_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'guidelines_pdf' => 'nullable|file|max:2048',
        ];
        return $rules;
    }

    protected function prepareForValidation()
    {
        // Convert empty strings to null for nullable fields
        $this->merge([
            'description' => $this->description ?: null,
            'instructions' => $this->instructions ?: null,
            'restrictions' => $this->restrictions ?: null,
            'additional_notes' => $this->additional_notes ?: null,
            
            // Cast to integer (0 or 1) with defaults
            'status' => $this->has('status') ? (int)$this->boolean('status') : 1, // Default 1
            'is_home_collection_available' => (int)$this->boolean('is_home_collection_available'), // Default 0
            'is_featured' => (int)$this->boolean('is_featured'), // Default 0
            'duration' => $this->duration ?: null,
            'test_report_time' => $this->test_report_time ?: null,
        ]);
    }

    public function messages()
    {
        $message =  [
            // Required validations
            'name.required' => __('messages.name_required'),
            'name.unique' => __('messages.name_unique'),
            'lab_id.required' => __('messages.lab_required'),
            'vendor_id.required' => __('messages.vendor_required'),
            'price.required' => __('messages.price_required'),
            'duration.required' => __('messages.duration_required'),
            'test_report_time.required' => __('messages.test_report_time_required'),
            'status.required' => __('messages.status_required'),
            'is_home_collection_available.required' => __('messages.home_collection_available_required'),
            'is_featured.required' => __('messages.featured_required'),
            'category_id.required' => __('messages.category_required'),

            // Exists validations
            'category_id.exists' => __('messages.category_invalid'),
            'lab_id.exists' => __('messages.lab_invalid'),
            'vendor_id.exists' => __('messages.vendor_invalid'),

            // Image validations
            'test_image.image' => __('messages.test_image_type'),
            'test_image.mimes' => __('messages.test_image_mimes'),
            'test_image.max' => __('messages.test_image_max'),
            'test_image.uploaded' => __('messages.test_image_uploaded'),

            // PDF validations
            'guidelines_pdf.file' => __('messages.guidelines_pdf_file'),
            'guidelines_pdf.mimes' => __('messages.guidelines_pdf_mimes'),
            'guidelines_pdf.max' => __('messages.guidelines_pdf_max'),
            'guidelines_pdf.uploaded' => __('messages.guidelines_pdf_uploaded'),

            // String validations with max length
            'name.max' => __('messages.name_max'),
            'code.max' => __('messages.code_max'),

            'description.max' => __('messages.description_max'),
            'instructions.max' => __('messages.instructions_max'),
            'restrictions.max' => __('messages.restrictions_max'),
            'additional_notes.max' => __('messages.additional_notes_max'),

            // String type validations
            'code.string' => __('messages.code_string'),
            'description.string' => __('messages.description_string'),
            'instructions.string' => __('messages.instructions_string'),
            'restrictions.string' => __('messages.restrictions_string'),
            'additional_notes.string' => __('messages.additional_notes_string'),

            // Boolean validations
            'status.boolean' => __('messages.status_boolean'),
            'is_home_collection_available.boolean' => __('messages.home_collection_available_boolean'),
            'is_featured.boolean' => __('messages.featured_boolean'),
            
            // Numeric validations
            'price.numeric' => __('messages.price_numeric'),
            'duration.numeric' => __('messages.duration_numeric'),

            // Equipment validations
            'equipment.required' => __('messages.equipment_required'),
            'equipment.array' => __('messages.equipment_array'),
            'equipment.*.string' => __('messages.equipment_string'),
            'equipment.*.max' => __('messages.equipment_max'),

            // Type validations
            'type.required' => __('messages.type_required'),
            'type.array' => __('messages.type_array'),
            'type.*.string' => __('messages.type_string'),
            'type.*.max' => __('messages.type_max'),
        ];
        return $message;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //     return true;
    // }
}
