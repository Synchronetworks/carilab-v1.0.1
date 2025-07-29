<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Commision\Models\CommissionEarning;

class CashPaymentHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        $collector_commission = CommissionEarning::where('commissionable_id', $this->appointment_id)
        ->where('user_type', 'collector')
        ->value('commission_amount');  // Returns a single value

        // Get the total sum of vendor commission
        $vendor_commission = CommissionEarning::where('commissionable_id', $this->appointment_id)
        ->where('user_type', 'vendor')
        ->value('commission_amount'); 
        return [
            'id'            => $this->id,
            'transaction_id'          => $this->transaction_id,
            'appointment_id'   => $this->appointment_id,
            'action'   => $this->action,
            'text'   => $this->text,
            'type'   => $this->type,
            'status'   => $this->status,
            'sender_id'=> $this->sender_id,
            'receiver_id'        => $this->receiver_id,
            'datetime' => $this->datetime,
            'total_amount' =>  $this->total_amount,
            'parent_id' =>  $this->parent_id,
            'vendor_id' => optional($this->appointment)->vendor_id ?? null,
            'collector_commission' => $collector_commission ?? 0,
            'vendor_commission' => $vendor_commission ?? 0,
            'payment_status' => optional($this->transaction)->payment_status ?? null,
        ];
    }
}
