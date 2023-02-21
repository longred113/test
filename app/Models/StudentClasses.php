<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClasses extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'studentId',
        'classId',
        'point',
    ];
}
