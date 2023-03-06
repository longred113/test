<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentMatchedActivityResource extends JsonResource
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
            'studentMatchedActivityId' => $this->studentMatchedActivityId,
            'studentId' => $this->studentId,
            'matchedActivityId' => $this->matchedActivityId,
            'name' => $this->name,
            'status' => $this->status,
        ];
    }
}
