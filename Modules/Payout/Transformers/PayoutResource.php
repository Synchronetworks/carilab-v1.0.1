<?php

namespace Modules\Payout\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;
class PayoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $noOfDecimal = Setting::getSettings('digitafter_decimal_point') ?? 2;
        return [
            'id'                => $this->id,
            'payment_method'    => $this->payment_method,
            'description'       => $this->description,
            'amount'            => round($this->amount ?? 0, $noOfDecimal),
            'created_at'        => Setting::formatDate($this->created_at).' '.Setting::formatTime($this->created_at) ,
            'updated_at'        => Setting::formatDate($this->updated_at).' '.Setting::formatTime($this->updated_at)  ,
        ];
    }
}
