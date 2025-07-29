<?php

namespace Modules\PackageManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatlogManagement\Transformers\CatlogResource;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\Review\Models\Review;


class PackageDetailResource extends JsonResource
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
            'original_price' => $this->price,
            'description' => $this->description,
            'status' => $this->status,
            'package_logo' => $this->getPackageImageAttribute(),
            'test_case_list' => CatlogResource::collection(CatlogManagement::whereIn('id', $this->packageCatlogMapping->pluck('catalog_id'))->get()),
            'is_discount' => $this->is_discount,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_price,
            'discounted_amount' => $this->getFinalDiscountPriceAttribute(),
            'package_available_till' =>$this->end_at,
            'is_home_collection_available' => $this->is_home_collection_available,
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
            'vendor_info' => [
                'id' => optional($this->vendor)->id,
                'name' => optional($this->vendor)->name,
                'email' => optional($this->vendor)->email,
                'phone' => optional($this->vendor)->phone,
                'address' => optional($this->vendor)->address,
                'profile_image' => setBaseUrlWithFileName(optional($this->vendor)->profile_image),    
            ],
            'other_lab_count' => $this->where('parent_id',$this->id)->count() ?? 0,
            'lab_list' => $this->where('parent_id',$this->id) ->with('lab')->get()
                ->map(function ($package) {
                    return $package->lab ? [
                        'lab_id' => $package->lab->id,
                        'lab_name' => $package->lab->name,
                        'lab_logo' => $package->lab->getLogoUrlAttribute(), 
                        'original_price' => $package->price ?? 0,
                    ] : null;
                })
                ->filter()->values() ?? null,
        ];
    }
}
