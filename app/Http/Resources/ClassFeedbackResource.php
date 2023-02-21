<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassFeedbackResource extends JsonResource
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
            'classId' => $this->classId,
            'studentId' => $this->studentId,
            'campusId' => $this->campusId,
            'date' => $this->date,
            'satisfaction' => $this->satisfaction,
            'comment' => $this->comment,
        ];
    }
}