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
            'writer' => $this->writer,
            'class' => $this->class,
            'title' => $this->title,
            'view' => $this->view,
            'date' => $this->date,
        ];
    }
}
