<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ParentResource extends JsonResource
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
            'parentId' => $this->parentId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'studentIds' => $this->studentIds,
        ];
    }
}
