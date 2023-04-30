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
            'name' => $this->name,
            'level' => $this->level,
            'details' => $this->details,
            'image' => $this->image,
            'duration' => $this->duration,
            'activate' => $this->activate,
        ];
    }
}