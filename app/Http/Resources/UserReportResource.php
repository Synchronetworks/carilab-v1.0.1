<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;
class UserReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'user_id'           => $this->user_id,
            'name'              => $this->name,
            'uploaded_at'       => Setting::formatDate($this->uploaded_at).' '.Setting::formatTime($this->uploaded_at),
            'additional_note'   => $this->additional_note,
            'attachments'        => getAttachments($this->getMedia('medical_report')),
            'attachments_array'  => getAttachmentArray($this->getMedia('medical_report'),null),
            'customer_info' => [
                'id' => optional($this->user)->id,
                'full_name' => optional($this->user)->first_name .' '.optional($this->user)->last_name,
                'image' => setBaseUrlWithFileName(optional($this->user)->profile_image),    
            ],
        ];
    }
}
