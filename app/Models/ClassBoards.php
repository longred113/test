<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassBoards extends Model
{
    use HasFactory;
    protected $primaryKey = 'classBoardId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'classBoardId',
        'message',
        'teacherId',
        'title',
        'studentId',
        'date',
        'type',
        'studentName',
        'teacherName',
    ];
}
