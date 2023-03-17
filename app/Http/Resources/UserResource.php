<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'userId' => $this->userId,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'roleId' => $this->roleId,
            'teacherId' => $this->teacherId,
            'studentId' => $this->studentId,
            'parentId' => $this->parentId,
            'CampusManagerId' => $this->CampusManagerId,
            'campusId' => $this->campusId,
            'activate' => $this->activate,
            'checkLogin' => $this->checkLogin,
        ];
    }
}
