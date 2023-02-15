<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampusManager extends Model
{
    use HasFactory;
    protected $primaryKey = 'campusManagerId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'campusManagerId',
        'name',
        'email',
        'gender',
        'dateOfBirth',
        'country',
        'timeZone',
        'startDate',
        'resignation',
        'campusId',
        'memo',
        'offlineStudentId',
        'offlineTeacherId',
    ];
}