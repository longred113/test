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
        'productId' => $this->productId,
        'name' => $this->name,
        'numberOfStudent' => $this->numberOfStudent,
        'subject' => $this->subject,
        'onlineTeacher' => $this->onlineTeacher,
        'classday' => $this->classday,
        'classTimeSlot' => $this->classTimeSlot,
        'classStartDate' => $this->classStartDate,
        'status' => $this->status,
        ];
    }
}
