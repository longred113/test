<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollments extends Model
{
    use HasFactory;
    protected $primaryKey = 'studentEnrollmentId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'studentEnrollmentId',
        'studentId',
        'enrollmentId',
        'date',
    ];
}
