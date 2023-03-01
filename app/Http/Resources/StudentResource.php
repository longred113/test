<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'gender' => $this->gender,
            'dateOfBirth' => $this->dateOfBirth,
            'country' => $this->country,
            'timeZone' => $this->timeZone,
            'status' => $this->status,
            'joinedDate' => $this->joinedDate,
            'withDrawal' => $this->withDrawal,
            'introduction' => $this->introduction,
            'talkSamId' => $this->talkSamId,
            'basicPoint' => $this->basicPoint,
            'campusId' => $this->campusId,
            'type' => $this->type,
        ];
    }
}
