<?php

namespace Modules\Collector\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Document\Models\Document;
class CollectorDocumentRequest extends FormRequest
{
    public function rules()
    {
        $isEditing = request()->id ? true : false;
        $documentId = $this->input('document_id');
        $document = Document::find($documentId);
        $isEdit = $this->id ?true:false;

        $documentRules = 'nullable|file|max:2048';
        if (!$isEditing && $document && $document->is_required == 1) {
            $documentRules = 'required|file|max:2048' ;
        }
  

        return [
            'collector_id' => 'required|exists:users,id',
            'document_id' => [
                'required',
                'exists:documents,id',
                Rule::unique('collector_documents')
                    ->where(function ($query) {
                        return $query->where('collector_id', $this->collector_id)
                            ->where('document_id', $this->document_id);
                    })
                    ->ignore($this->id),
            ],
            'collector_document' => $documentRules,
            'is_verified' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'document_id.unique' => __('messages.document_id_unique'),
            'collector_document.required' => __('messages.collector_document_required'),
            'collector_document.mimes' => __('messages.collector_document_mimes'),
            'collector_document.max' => __('messages.collector_document_max'),
            'collector_id.required' => __('messages.collector_id_required'),
            'collector_id.exists' => __('messages.collector_id_exists'),
            'document_id.required' => __('messages.document_id_required'),
            'document_id.exists' => __('messages.document_id_exists'),
            'is_verified.boolean' => __('messages.is_verified_boolean'),
            'status.boolean' => __('messages.status_boolean'),
            'collector_document.file' => __('messages.collector_document_file'),
        ];
    }
}