<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductEnrollmentResource extends JsonResource
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
            'productEnrollmentId' => $this->productEnrollmentId,
            'productId' => $this->productId,
            'enrollmentId' => $this->enrollmentId,
            'date' => $this->date,
        ];
    }
}
