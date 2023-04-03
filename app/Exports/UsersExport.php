<?php

namespace App\Exports;

use App\Models\Users;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class UsersExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $user = Users::leftJoin('teachers', 'users.teacherId', '=', 'teachers.teacherId')
            ->leftJoin('students', 'users.studentId', '=', 'students.studentId')
            ->leftJoin('parents', 'users.parentId', '=', 'parents.parentId')
            ->leftJoin('campus_managers', 'users.campusManagerId', '=', 'campus_managers.campusManagerId')
            ->leftJoin('campuses', 'users.campusId', '=', 'campuses.campusId')
            ->leftJoin('roles', 'users.roleId', '=', 'roles.roleId')
            ->select(
                'users.userId',
                'users.name',
                'users.email',
                'users.userName',
                'users.password',
                'users.roleId',
                'roles.name as roleName',
                'users.teacherId',
                'teachers.name as teacherName',
                'users.studentId',
                'students.name as studentName',
                'users.parentId',
                DB::raw("CONCAT(parents.firstName,' ', parents.lastName) as parentName"),
                'users.campusManagerId',
                'campus_managers.name as campusManagerName',
                'users.campusId',
                'campuses.name as campusName',
                'users.activate',
                'users.checkLogin',
                'users.created_at',
                'users.updated_at'
            )->get();
        // foreach ($user as $key => $value) {
        //     // $user[$key]['activate'] = $value['activate'] == 1 ? 'Active' : 'Inactive';
        //     // $user[$key]['checkLogin'] = $value['checkLogin'] == 1 ? 'Yes' : 'No';
        //     try{
        //         if ($value->checkLogin == 0) {
        //             $user[$key]->checkLogin = 0;
        //             $user[$key]->formatCells(function($cells) {
        //                 $cells->setFontColor('#FFFFFF')
        //                       ->setBackground('#FF0000');
        //             });
        //         }
        //     }catch(Exception $e){
        //         dd($e->getMessage());
        //     }
        // }
        return $user;
    }

    public function headings(): array
    {
        return [
            'userId',
            'name',
            'username',
            'email',
            'password',
            'roleId',
            'roleName',
            'teacherId',
            'teacherName',
            'studentId',
            'studentName',
            'parentId',
            'parentName',
            'campusManagerId',
            'campusManagerName',
            'campusId',
            'campusName',
            'active',
            'checkLogin',
            'created_at',
            'updated_at',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'T' => NumberFormat::FORMAT_TEXT, // Định dạng cột checkLogin thành dạng text
        ];
    }
}
