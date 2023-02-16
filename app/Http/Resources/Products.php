<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Products extends JsonResource
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
            'productId' => $this->productId,
            'packageId' => $this->packageId,
            'name' => $this->name,
            'startLevel' => $this->startLevel,
            'endLevel' => $this->endLevel,
            'details' => $this->details,
            'image' => $this->image,
            'activate' => $this->activate,
        ];
    }
}