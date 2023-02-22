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
        'studentName',
        'talkSamId',
        'campusName',
        'activate',
        'level',
        'subject',
        'status',
        'submitted',
    ];
}