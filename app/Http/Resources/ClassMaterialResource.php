<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassMaterialResource extends JsonResource
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
            'classMaterialId' => $this->classMaterialId,
            'writer' => $this->writer,
            'class' => $this->class,
            'title' => $this->title,
            'view' => $this->view,
            'date' => $this->date,
        ];
    }
}
