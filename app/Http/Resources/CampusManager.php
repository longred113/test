<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampusManager extends JsonResource
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
        'campusManagerId' => $this->campusManagerId,
        'name' => $this->name,
        'email'=>$this->email,
        'gender'=>$this->gender,
        'dateOfBirth'=>$this->dateOfBirth->format('d/m/Y'),
        'country'=>$this->country,
        'timeZone'=>$this->timeZone,
        'startDate'=>$this->startDate,
        'resignation'=>$this->resignation,
        'campusId'=>$this->campusId,
        'memo'=>$this->memo,
        ];
    }
}
