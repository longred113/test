<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivateMessageResource extends JsonResource
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
            'id' => $this->id,
            'userName' => $this->userName,
            'studentId' => $this->studentId,
            'teacherId' => $this->teacherId,
            'message' => $this->message,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
        ];
    }
}
