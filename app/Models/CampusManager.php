<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampusManager extends Model
{
    use HasFactory;
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
        'memo'
    ];
    protected $primaryKey = 'campusManagerId';
}