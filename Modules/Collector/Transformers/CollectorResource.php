<?php

namespace Modules\Coupon\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'lab_id' => $this->lab_id,
            'coupon_code' => $this->coupon_code,
            'discount_type'=>$this->discount_type,
            'discount_value'=>$this->discount_value,
            'status'=>$this->status,
        
        ];
    }
}
