<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    use HasFactory;
    protected $primaryKey = 'studentId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'studentId',
        'name',
        'email',
        'gender',
        'dateOfBirth',
        'country',
        'timeZone',
        'status',
        'joinedDate',
        'withDrawal',
        'introduction',
        'talkSamId',
        'basicPoint',
        'campusId',
        'type',
        'classId',
    ];
}