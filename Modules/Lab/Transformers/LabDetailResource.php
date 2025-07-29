<?php

namespace Modules\Lab\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LabDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'lab_code' => $this->lab_code,
            'lab_name' => $this->name,
            'description' => $this->description,
            'lab_email' => $this->email,
            'lab_phone' => $this->phone_number,
            'lab_logo' => $this->getLogoUrlAttribute(),
            'address' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'payment_method_list' => $this->payment_modes,
            'payment_gateway' => $this->payment_gateways,
            'lab_full_address' => trim(
                    implode(
                        ', ',
                        array_filter([
                            $this->address_line_1 ?? null,
                            $this->address_line_2 ?? null,
                            $this->city->name ?? null,
                            $this->state->name ?? null,
                            $this->country->name ?? null,
                            $this->postal_code ?? null,
                        ]),
                    ),
                ),
            'vendor_info' => [
                'id' => optional($this->vendor)->id,
                'full_name' => optional($this->vendor)->getFullNameAttribute(),
                'email' => optional($this->vendor)->email,
                'phone' => optional($this->vendor)->phone,
                'address' => optional($this->vendor)->address,
                'profile_image' => setBaseUrlWithFileName(optional($this->vendor)->profile_image),   
                'tax' => optional($this->vendor)->userTaxMapping
                ->filter(fn($taxMapping) => $taxMapping->tax->status !== 0) // Exclude taxes with status 0
                ->map(fn($taxMapping) => [
                    'id' => optional($taxMapping->tax)->id ?? null,
                    'name' => optional($taxMapping->tax)->title ?? '',
                    'type' => optional($taxMapping->tax)->type ?? 0,
                    'value' => optional($taxMapping->tax)->value ?? 0,
                ])  
            ],
            'slot_duration_difference' => $this->time_slot,
            'available_time_slots' => $this->getLabSession($this->labSessions),
            'certified_by' => $this->accreditation_type ?? '',
            'certification_expired_date' => $this->accreditation_expiry_date ? $this->accreditation_expiry_date->format('Y-m-d') : '',
            'total_packages' => $this->getDistinctTestPackageCount() ?? 0,
            'total_test_cases' => $this->getDistinctTestCaseCount() ?? 0,
            'total_reviews' => $this->getTotalReviews() ?? 0,
            'tax' => $this->labTaxMapping
            ->filter(fn($taxMapping) => $taxMapping->tax->status !== 0) // Exclude taxes with status 0
            ->map(fn($taxMapping) => [
                'id' => optional($taxMapping->tax)->id ?? null,
                'name' => optional($taxMapping->tax)->title ?? '',
                'type' => optional($taxMapping->tax)->type ?? 0,
                'value' => optional($taxMapping->tax)->value ?? 0,
            ])
           
        ];
    }
}
