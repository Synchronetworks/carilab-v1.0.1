<?php

namespace Modules\Bank\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'bank_name' => $this->bank_name,
            'branch_name' => $this->branch_name,
            'account_no'=>$this->account_no,
            'phone_number'=>$this->phone_number,
            'ifsc_code'=>$this->ifsc_code,
            'status'=>$this->status,
            'is_default'=>$this->is_default,
            'bank_attachment' => $this->getFirstMediaUrl('bank_attachment') ?? null,
        
        ];
    }
}
