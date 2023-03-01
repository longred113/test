<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClassMatchedActivities extends Model
{
    use HasFactory;
    protected $primaryKey = 'studentClMaActivityId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'studentClMaActivityId',
        'studentId',
        'matchedActivityId',
        'classId',
        'status',
    ];
}
