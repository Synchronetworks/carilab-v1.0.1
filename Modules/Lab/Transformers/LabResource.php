<?php

namespace Modules\Lab\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LabResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
public function toArray($request)
{
    return [
        'lab_id' => $this->id,
        'lab_name' => $this->name,
        'lab_email' => $this->email,
        'lab_phone' => $this->phone_number,
        'status' => $this->status,
        'address' => $this->address_line_1,
        'address_line_2' => $this->address_line_2,
        'lab_logo' => $this->getLogoUrlAttribute(),
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
       'tax' => $this->labTaxMapping
            ->filter(fn($taxMapping) => $taxMapping->tax->status !== 0) // Exclude taxes with status 0
            ->map(fn($taxMapping) => [
                'id' => optional($taxMapping->tax)->id ?? null,
                'name' => optional($taxMapping->tax)->title ?? '',
                'type' => optional($taxMapping->tax)->type ?? 0,
                'value' => optional($taxMapping->tax)->value ?? 0,
            ]),
     'vendor_info' => [
                'id' => optional($this->vendor)->id,
                'full_name' => optional($this->vendor)->getFullNameAttribute(),
                'email' => optional($this->vendor)->email,
                'phone' => optional($this->vendor)->phone,
                'address' => optional($this->vendor)->address,
                'profile_image' => setBaseUrlWithFileName(optional($this->vendor)->profile_image),
                'tax' =>  optional($this->vendor)->userTaxMapping !== null
                ? $this->vendor->userTaxMapping
                    ->filter(fn($taxMapping) => optional($taxMapping->tax)->status !== 0)
                    ->map(fn($taxMapping) => [
                        'id' => optional($taxMapping->tax)->id,
                        'name' => optional($taxMapping->tax)->title ?? '',
                        'type' => optional($taxMapping->tax)->type ?? 0,
                        'value' => optional($taxMapping->tax)->value ?? 0,
                    ])->values()
                : null,
            ],

    ];
}
}
