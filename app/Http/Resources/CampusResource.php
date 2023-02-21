<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampusResource extends JsonResource
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
            'campusId' => $this->campusId,
            'name' => $this->name,
            'indicated' => $this->indicated,
            'contact' => $this->contact,
<<<<<<< HEAD
            'activate' => $this->activate,
=======
>>>>>>> 8c921956d2c9cf2cfbc93d33eb35c28f8fd16936
        ];
    }
}
