<?php

namespace Modules\Helpdesk\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;
use DateTime;

class HelpDeskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'subject'           => $this->subject,
            'user_id'           => $this->user_id,
            'description'       => $this->description,
            'mode'              => $this->mode,
            'contact_number'    => $this->contact_number ?? optional($this->users)->contact_number,
            'email'             => $this->email ?? optional($this->users)->email,
            'created_at'        => Setting::formatDate($this->created_at).' '.Setting::formatTime($this->created_at) ,
            'updated_at'        => Setting::formatDate($this->updated_at).' '.Setting::formatTime($this->updated_at)  ,
            'employee_name'     => optional($this->users)->first_name.' '.optional($this->users)->last_name ?? 'unknown',
            'user_type'         => optional($this->users)->user_type ?? '',
            'employee_image'    => getSingleMedia($this->users, 'profile_image',null),
            'status'            => $this->status == 0 ? 'open' : 'closed',
            'attachments' => getAttachments($this->getMedia('helpdesk_attachment')),
            'attachments_array' => getAttachmentArray($this->getMedia('helpdesk_attachment'),null),
            
        ];
    }
}