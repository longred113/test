<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassFeedbacks extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacherId',
        'classId',
        'studentId',
        'campusId',
        'date',
        'satisfaction',
        'comment',
    ];
}
