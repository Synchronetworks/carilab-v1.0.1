<?php

namespace Modules\Helpdesk\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HelpdeskRequest extends FormRequest
{
    public function rules()
    {
        $user = auth()->user(); // Get the authenticated user
    
        $rules = [
            'subject'             => 'required',
            'description'         => 'required',
            'helpdesk_attachment' => 'nullable|array', // Ensure it's an array
            'helpdesk_attachment.*' => 'image|mimes:jpeg,png,jpg,svg|max:2048',
        ];
    
        // Apply 'mode' as required only for 'admin' or 'demo_admin' roles
        if ($user && in_array($user->role, ['admin', 'demo_admin'])) {
            $rules['mode'] = 'required|in:email,phone,other';
        } else {
            $rules['mode'] = 'nullable|in:email,phone,other'; // Optional for others
        }
    
        return $rules;
    }


    public function messages()
    {
        return [
            'subject.required' => __('messages.subject_required'),
            'description.required' => __('messages.description_required'),
            'mode.required' => __('messages.mode_required'),
            'name.required' => __('messages.name_required'),
            'helpdesk_attachment.*.image' => __('messages.helpdesk_attachment_image'),
            'helpdesk_attachment.*.mimes' => __('messages.helpdesk_attachment_mimes'),
            'helpdesk_attachment.*.max' => __('messages.helpdesk_attachment_max'),
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
