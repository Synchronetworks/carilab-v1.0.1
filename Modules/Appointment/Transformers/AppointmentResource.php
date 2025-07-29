<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
        public function toArray($request)
        {
            return [
                'id' => $this->id,
                'collection_type' => $this->collection_type,
                'amount' => $this->amount,
                'paid_amount' => $this->total_amount,
                'status' => $this->status,
                'appointment_date' => Setting::formatDate($this->appointment_date),
                'appointment_time' => Setting::formatTime($this->appointment_time),
                'lab_info'=>[
                    'lab_id' => optional($this->lab)->id,
                    'lab_name' => optional($this->lab)->name,
                    'lab_logo' => optional($this->lab)->getLogoUrlAttribute(),
                ],
                'test_case_info'=>[
                    'test_type' => $this->test_type,
                    'testcase_name' => $this->test_type == 'test_case' ? optional($this->catlog)->name : optional($this->package)->name,
                ],
                'vendor_info' => [
                    'id' => optional($this->vendor)->id,
                    'full_name' => optional($this->vendor)->full_name,
                    'profile_image' => setBaseUrlWithFileName(optional($this->vendor)->profile_image),
                    'is_verified_user' =>  optional($this->vendor)->is_verify,    
                ],
                'collector_info' => [
                    'id' => optional(optional($this->appointmentCollectorMapping)->collector)->id,
                    'full_name' => optional(optional($this->appointmentCollectorMapping)->collector)->full_name,
                    'profile_image' => setBaseUrlWithFileName(optional(optional($this->appointmentCollectorMapping)->collector)->profile_image),
                    'is_verified_user' =>  optional(optional($this->appointmentCollectorMapping)->collector)->is_verify,    
                ],
                'customer_info' => [
                    'id' => optional($this->customer)->id,
                    'full_name' => optional($this->customer)->full_name,
                    'profile_image' => setBaseUrlWithFileName(optional($this->customer)->profile_image),
                    'is_verified_user' =>  optional($this->customer)->is_verify,   
                ],
                'payment' => $this->transactions,
                'reschedule_reason' => $this->reschedule_reason ? $this->reschedule_reason : null,
            ];
        }
    }
