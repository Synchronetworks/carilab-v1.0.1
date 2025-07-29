<?php

namespace Modules\CatlogManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CatlogDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'test_case_id' => $this->id,
            'test_code' => $this->test_code,
            'test_case_name' => $this->name,
            'description' => $this->description,
            'category' => optional($this->category)->name,
            'duration' => $this->duration,
            'lab' => optional($this->lab)->name,
            'original_price' => $this->price,
            'is_home_collection_available' => $this->is_home_collection_available,
            'test_case_image' => $this->getTestImageAttribute(),
            'test_guidelines_pdf' => $this->getGuidelinesPdfAttribute(),
            'test_type' =>$this->type,
            'test_equipment' =>$this->equipment,
            'lab_info' => [
                'lab_name' => optional($this->lab)->name,
                'address' => optional($this->lab)->address,
                'lab_phone' => optional($this->lab)->phone_number,
                'lab_email' => optional($this->lab)->email,
                'latitude' => optional($this->lab)->latitude,
                'longitude' => optional($this->lab)->longitude,
                'lab_logo' => optional($this->lab)->getLogoUrlAttribute(),
            ],
            'vendor_info' => [
                'id' => optional($this->vendor)->id,
                'full_name' => optional($this->vendor)->full_name,
                'email' => optional($this->vendor)->email,
                'mobile' => optional($this->vendor)->mobile,
                'address' => optional($this->vendor)->address,
                'profile_image' => setBaseUrlWithFileName(optional($this->vendor)->profile_image),    
            ],
            'test_instruction' => $this->instructions,
            'test_restrictions' => $this->restrictions,
            'additional_notes' => $this->additional_notes,
            'package_suggestion_list' => optional($this->packageCatlogMapping)
                ? $this->packageCatlogMapping
                    ->map(fn($mapping) => $mapping->package) 
                    ->filter() 
                    ->where('status', 1) 
                    ->map(fn($package) => [
                        'id' => $package->id,
                        'name' => $package->name ?? '',
                        'price' => $package->price ?? 0,
                        'start_at' => $package->start_at ?? '',
                        'end_at' => $package->end_at ?? '',
                        'discount_type' => $package->discount_type ?? '',
                        'discount_price' => $package->discount_price ?? '',
                    ])
                    ->values() 
                : [],
            'other_lab_count' => $this->where('parent_id',$this->id)->count() ?? 0,
            'lab_list' => $this->where('parent_id',$this->id) ->with('lab')->get()
                ->map(function ($catalog) {
                    return $catalog->lab ? [
                        'lab_id' => $catalog->lab->id,
                        'lab_name' => $catalog->lab->name,
                        'lab_logo' => $catalog->lab->getLogoUrlAttribute(), 
                        'original_price' => $catalog->price ?? 0,
                    ] : null;
                })
                ->filter()->values() ?? null,


        ];
    }
}
