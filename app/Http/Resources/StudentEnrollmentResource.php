<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentEnrollmentResource extends JsonResource
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
            'studentEnrollmentId' => $this->studentEnrollmentId,
            'studentId' => $this->studentId,
            'enrollmentId' => $this->enrollmentId,
            'date' => $this->date,
        ];
    }
}
