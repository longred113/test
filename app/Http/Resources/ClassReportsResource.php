<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassReportsResource extends JsonResource
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
            'classReportId' => $this->classReportId,
            'teacherId' => $this->teacherId,
            'classId' => $this->classId,
            'studentId' => $this->studentId,
            'campusId' => $this->campusId,
            'status' => $this->status,
            'date' => $this->date,
            'preparation' => $this->preparation,
            'attitude' => $this->attitude,
            'participation' => $this->participation,
            'comment' => $this->comment,
        ];
    }
}