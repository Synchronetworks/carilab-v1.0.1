<?php

namespace Modules\Prescription\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'lab_id' => $this->lab_id,
            'uploaded_at' => $this->uploaded_at,
            'note' => $this->note,
            'prescription_status' => $this->prescription_status,
            'is_notify' => $this->is_notify,
            'status' => $this->status,
            'prescription_upload' => $this->getPrescriptionUploadUrlAttribute(),
            'customer_info' => [
                'id' => optional($this->user)->id,
                'full_name' => optional($this->user)->first_name .' '.optional($this->user)->last_name,
                'image' => setBaseUrlWithFileName(optional($this->user)->profile_image),    
            ],
          

            'lab_info' => optional($this->labMappings->first(), function ($mapping) {
                    return [
                        'lab_id' => optional($mapping->lab)->id,
                        'lab_name' => optional($mapping->lab)->name,
                        'lab_logo' => optional($mapping->lab)->getLogoUrlAttribute(),
                        'lab_phone' => optional($mapping->lab)->phone,
                        'lab_email' => optional($mapping->lab)->email,
                        'address' => optional($mapping->lab)->address,
                        'payment_method_list' => optional($mapping->lab)->payment_modes,
                        'payment_gateway' => optional($mapping->lab)->payment_gateways,
                        'test_type' => optional($mapping->testMapping)->type,
                        'test_case' => optional($mapping->testMapping)->type == 'test_case' 
                            ? optional(optional($mapping->testMapping)->catalog)->name 
                            : optional(optional($mapping->testMapping)->package)->name,
                    ];
                }),

        ];
    }
}
