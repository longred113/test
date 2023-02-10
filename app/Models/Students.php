<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    use HasFactory;
    protected $fillable = [
        'studentId',
        'name',
        'email',
        'gender',
        'dateOfBirth',
        'country',
        'timeZone',
        'joinedDate',
        'withDrawal',
        'introduction',
        'talkSamId',
        'basicPoint',
        'campusId'
    ];
}
