<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
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
            'unitId' => $this->unitId,
            'matchedActivityId' => $this->matchActivityId,
            'productId' => $this->productId,
            'name' => $this->name,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];
    }
}
