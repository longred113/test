<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
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
            'teacherId' => $this->teacherId,
            'name' => $this->name,
            'email' => $this->email,
            'gender'=> $this->gender,
            'dateOfBirth' => $this->dateOfBirth,
            'status' => $this->status,
            'activate' => $this->activate,
            'country' => $this->country,
            'timeZone' => $this->timeZone,
            'startDate'=> $this->startDate,
            'resignation' => $this->resignation,
            'resume' => $this->resume,
            'certificate'=> $this->certificate,
            'contract' => $this->contract,
            'basicPoint' => $this->basicPoint,
            'campusId' => $this->campusId,
            'type' => $this->type,
            'talkSamId' => $this->talkSamId,
            'role' => $this->role,
            'memo' => $this->memo,
        ];
    }
}