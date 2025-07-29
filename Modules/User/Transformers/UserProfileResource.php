<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
       

        return [

            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name.' '.$this->last_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'gender' => $this->gender,
            'dob' => $this->date_of_birth,
            'login_type' => $this->login_type,
            'email_verified_at' => $this->email_verified_at,
            'is_banned' => $this->is_banned,
            'status' => $this->status,
            'last_notification_seen' => $this->last_notification_seen,
            'is_user_exist' => true,
            'profile_image' => $this->getFirstMediaUrl('profile_image'),
            'social_image' => $this->social_image ?? null,
            'is_available' => $this->is_available ?? 0,
            'is_verified_user' =>  $this->is_verify, 
            'address' => $this->address,
            'full_address' => trim(
                    implode(
                        ', ',
                        array_filter([
                            $this->address ?? null,
                            $this->city->name ?? null,
                            $this->state->name ?? null,
                            $this->country->name ?? null,
                        ]),
                    ),
                ),

        ];
    }
}
