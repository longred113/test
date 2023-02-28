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
            'enrollmentId' => $this->enrollmentId,
            'studentId' => $this->studentId,
            'talkSamId' => $this->talkSamId,
<<<<<<< HEAD
            'campusName' => $this->campusName,
=======
            'campusId' => $this->campusId,
            'activate' => $this->activate,
>>>>>>> 94f6a75f8843d825637b59b4a25a7b0f76274569
            'level' => $this->level,
            'subject' => $this->subject,
            'status' => $this->status,
            'submittedDate' => $this->submittedDate,
        ];
    }
}