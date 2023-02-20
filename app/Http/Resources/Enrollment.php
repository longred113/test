<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Enrollment extends JsonResource
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
            'studentName' => $this->studentName,
            'talkSamId' => $this->talkSamId,
            'campusName' => $this->campusName,
            'activate' => $this->activate,
            'level' => $this->level,
            'subject' => $this->subject,
            'status' => $this->status,
        ];
    }
}