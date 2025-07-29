<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'appointment_id' => $this->appointment_id,
            'customer_id'   => optional($this->appointment)->customer_id,
            'total_amount'  => $this->total_amount,
            'payment_status'=> $this->payment_status,
            'payment_type'  => $this->payment_type,
            'payment_status'=> $this->payment_status,
            'customer_name' => optional(optional($this->appointment)->customer)->full_name,
            'taxes'         => json_decode($this->tax,true),
            'total_tax_amount' => $this->total_tax_amount,
            'discount_type' => $this->discount_type,
            'discount_value'=> $this->discount_value,
            'discount_amount'=> $this->discount_amount,
            'coupon'        => json_decode($this->coupon,true),
            'coupon_amount' => $this->coupon_amount,
            'price'         => isset($this->appointment) ? optional($this->appointment)->test_discount_amount : 0,
            'date'          => $this->appointment_date,
            'time'          => $this->appointment_time,
            'txn_id'        => $this->txn_id

        ];
    }
}
