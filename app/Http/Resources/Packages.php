<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Packages extends JsonResource
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
            'packageId' => $this->packageId,
            'name' => $this->name,
            'startLevel' => $this->startLevel,
            'endLevel' => $this->endLevel,
            'activate' => $this->activate,
            'details' => $this->details,
        ];
    }
}