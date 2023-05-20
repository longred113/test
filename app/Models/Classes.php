<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;
    protected $primaryKey = 'classId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'classId',
        'name',
        'level',
        'numberOfStudent',
        'subject',
        'onlineTeacher',
        'classday',
        'classTimeSlot',
        'classTime',
        'classStartDate',
        'classEndDate',
        'duration',
        'status',
        'typeOfClass',
        'initialTextbook',
        'unit',
        'campusId',
        'availableNumStudent',
        'expired',
    ];
}
