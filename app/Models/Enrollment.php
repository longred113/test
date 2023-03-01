<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $primaryKey = 'enrollmentId';
    protected $keyType = 'string';
    public $incrementing = false;
    use HasFactory;
    protected $fillable = [
        'enrollmentId',
        'studentId',
        'talkSamId',
<<<<<<< HEAD
        'campusName',
=======
        'campusId',
        'activate',
>>>>>>> 94f6a75f8843d825637b59b4a25a7b0f76274569
        'level',
        'subject',
        'status',
        'submittedDate',
    ];
}