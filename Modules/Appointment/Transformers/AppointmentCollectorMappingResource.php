<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Review\Models\Review;

class AppointmentCollectorMappingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {

        $reviewCount = Review::where('collector_id', $this->id)->count();
        $reviewSum = Review::where('collector_id', $this->id)->sum('rating');
        
        $averageRating = $reviewCount > 0 ? $reviewSum / $reviewCount : 0;

        return [
            'id' => $this->collector_id,
            'full_name'=>optional($this->collector)->first_name.' '.optional($this->collector)->last_name,
            'profile_image' => setBaseUrlWithFileName(optional($this->collector)->profile_image),    
            'mobile'=>optional($this->collector)->mobile,
            'email'=>optional($this->collector)->email,
            'rating'=>$averageRating,
            'is_verified_user' =>  optional($this->collector)->is_verify,
            
          
        ];
    }
}
