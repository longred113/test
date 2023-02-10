<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teachers extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacherId',
        'name',
        'email',
        'gender',
        'dateOfBirth',
        'status',
        'activate',
        'country',
        'timeZone',
        'startDate',
        'resignation',
        'resume',
        'certificate',
        'contract',
        'basicPoint',
        'campusId'
    ];
}
