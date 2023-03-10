<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Student extends JsonResource
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
            'studentId' => $this->studentId,
            'enrollmentId' => $this->enrollmentId,
            'parentId' => $this->parentId,
            'name' => $this->name,
            'email' => $this->email,
            'gender'=> $this->gender,
            'dateOfBirth' => $this->dateOfBirth,
            'country' => $this->country,
            'timeZone' => $this->timeZone,
            'joinedDate' => $this->joinedDate,
            'withDrawal' => $this->withDrawal,
            'introduction' => $this->introduction,
            'talkSamId' => $this->talkSamId,
            'basicPoint' => $this->basicPoint,
            'campusId' => $this->campusId,
            'type' => $this->type,
            'status' => $this->status,          
        ];
    }
}