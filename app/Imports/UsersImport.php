<?php

namespace App\Imports;

use App\Models\Teachers;
use App\Models\Users;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            Users::create([
                'userId' => $row['userId'],
                'name' => $row['name'],
                'email' => $row['email'],
                'username' => $row['username'],
                'password' => $row[2],
                'role' => $row['roleId'],
                'teacherId' => $row['teacherId'],
                'studentId' => $row['studentId'],
                'parentId' => $row['parentId'],
                'campusManagerId' => $row['campusManagerId'],
                'campusId' => $row['campusId'],
                'active' => $row['active'],
                'checkLogin' => $row['checkLogin'],
            ]);
        }
    }
}
