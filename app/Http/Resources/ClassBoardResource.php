<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassBoardResource extends JsonResource
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
            'classBoardId' => $this->classBoardId,
            'message' => $this->message,
            'teacherId' => $this->teacherId,
            'title' => $this->title,
            'studentId' => $this->studentId,
            'date' => $this->date,
            'studentName' => $this->studentName,
            'teacherName' => $this->teacherName,
            'type' => $this->type,
            'created_at' => $this->created_at,
        ];
    }
}
