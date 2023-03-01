<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchedActivityResource extends JsonResource
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
            'matchedActivityId' => $this->matchedActivityId,
            'productId' => $this->productId,
            'name' => $this->name,
            'time' => $this->time,
            'unitId' => $this->unitId,
        ];
    }
}
