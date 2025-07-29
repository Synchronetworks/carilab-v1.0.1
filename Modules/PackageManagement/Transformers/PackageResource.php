<?php

namespace Modules\PackageManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatlogManagement\Transformers\CatlogResource;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\Review\Models\Review;


class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */

    
    public function toArray($request)
    {
        $reviewCount = Review::where('lab_id', ($this->lab)->id)->count();
        $reviewSum = Review::where('lab_id', ($this->lab)->id)->sum('rating');
        
        $averageRating = $reviewCount > 0 ? $reviewSum / $reviewCount : 0;
        
        return [
            'package_id' => $this->id,
            'package_name' => $this->name,
            'package_logo' => $this->getPackageImageAttribute(),
            'original_price' => $this->price,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_price,
            'discounted_amount' => $this->getFinalDiscountPriceAttribute(),
            'test_case_list' => CatlogResource::collection(CatlogManagement::whereIn('id', $this->packageCatlogMapping->pluck('catalog_id'))->take(3)->get()),
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
            'package_end_date' => $this->end_at,
            'is_home_collection_available' => $this->is_home_collection_available,
            'parent_id' => $this->parent_id ?? null,
            'other_lab_count' => $this->where('parent_id',$this->id)->count() ?? 0,
            'lab_list' => $this->where('parent_id',$this->id) ->with('lab')->get()
                ->map(function ($package) {
                    return $package->lab ? [
                        'lab_id' => $package->lab->id,
                        'lab_name' => $package->lab->name,
                        'lab_logo' => $package->lab->getLogoUrlAttribute(), 
                    ] : null;
                })
                ->filter()->values() ?? null,
        ];
    }
}
