<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassResource extends JsonResource
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
            'classId' => $this->classId,
            'name' => $this->name,
            'level' => $this->level,
            'numberOfStudent' => $this->numberOfStudent,
            'onlineTeacher' => $this->onlineTeacher,
            'classday' => $this->classday,
            'classTimeSlot' => $this->classTimeSlot,
            'classTime' => $this->classTime,
            'classStartDate' => $this->classStartDate,
            'classEndDate' => $this->classEndDate,
            'duration' => $this->duration,
            'status' => $this->status,
            'initialTextbook' => $this->initialTextbook,
            'typeOfClass' => $this->typeOfClass,
            'unit' => $this->unit,
            'category' => $this->category,
            'campusId' => $this->campusId,
            'availableNumStudent' => $this->availableNumStudent,
            'expired' => $this->expired,
        ];
    }
}
