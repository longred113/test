<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassMatchActivityResource extends JsonResource
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
            'classMatchActivityId' => $this->classMatchActivityId,
            'classId' => $this->classId,
            'matchedActivityId' => $this->matchedActivityId,
            'status' => $this->status,
        ];
    }
}
