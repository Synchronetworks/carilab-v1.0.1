<?php

namespace Modules\Review\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'lab_id' => $this->lab_id,
            'collector_id' => $this->collector_id,
            'user_id' => $this->user_id,
            'rating' => $this->rating,
            'review' => $this->review,
            'type' => $this->type,
            'updated_at' => Setting::formatDate($this->updated_at).' '.Setting::formatTime($this->updated_at),
            'full_name' => optional($this->user)->getFullNameAttribute(),
            'profile_image' => optional($this->user)->profile_image,
            'collector_info'=>[
                'full_name' => optional($this->collector)->getFullNameAttribute(),
                'profile_image' => setBaseUrlWithFileName(optional($this->collector)->profile_image),    
            ],
            'lab_info'=>[
                'profile_image' => optional($this->lab)->getLogoUrlAttribute(),
                'name' => optional($this->lab)->name,
            ],

        ];
    }
}
