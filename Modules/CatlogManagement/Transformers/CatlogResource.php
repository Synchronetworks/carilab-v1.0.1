<?php

namespace Modules\CatlogManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CatlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'test_case_id' => $this->id,
            'test_case_name' => $this->name,
            'description' => $this->description,
            'category' => optional($this->category)->name,
            'category_id' => optional($this->category)->id,
            'duration' => $this->duration,
            'original_price' => $this->price,
            'is_home_collection_available' => $this->is_home_collection_available,
            'test_case_image' => $this->getTestImageAttribute(),
            'lab_info' => [
                'lab_id' => optional($this->lab)->id,
                'lab_name' => optional($this->lab)->name,
                'lab_logo' => optional($this->lab)->getLogoUrlAttribute(),
                'vendor_info' => [
                    'id' => optional(optional($this->lab)->vendor)->id,
                    'full_name' => optional(optional($this->lab)->vendor)->full_name,
                    'email' => optional(optional($this->lab)->vendor)->email,
                ],

            ],
            'parent_id' => $this->parent_id ?? null,
            'other_lab_count' => $this->where('parent_id',$this->id)->count() ?? 0,
            'lab_list' => $this->where('parent_id',$this->id) ->with('lab')->get()
                ->map(function ($catalog) {
                    return $catalog->lab ? [
                        'lab_id' => $catalog->lab->id,
                        'lab_name' => $catalog->lab->name,
                        'lab_logo' => $catalog->lab->getLogoUrlAttribute(), 
                    ] : null;
                })
                ->filter()->values() ?? null,

        ];
    }
}
