<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherOffDates extends Model
{
    protected $primaryKey = 'teacherOffDateId';
    protected $keyType = 'string';
    public $incrementing = false;
    use HasFactory;
    protected $fillable = [
        'teacherOffDateId',
        'teacherId',
        'date',
        'day',
        'classTimeSlotId',
    ];
}
