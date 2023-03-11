<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassReports extends Model
{
    use HasFactory;
    protected $primaryKey = 'classReportId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'classReportId',
        'teacherId',
        'classId',
        'studentId',
        'campusId',
        'status',
        'attendance',
        'date',
        'preparation',
        'attitude',
        'participation',
        'comment',
    ];
}