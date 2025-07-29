<?php

namespace Modules\Page\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'   => $this->id,
            'question' => $this->question,
            'answer' => $this->answer,
            'status'  => $this->status,
            'created_by'=> $this->created_by,
            'updated_by' =>$this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'feature_image' => $this->feature_image,
            'media' => $this->media
        ];
    }
}
