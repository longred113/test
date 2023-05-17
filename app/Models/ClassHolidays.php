<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassHolidays extends Model
{
    protected $primaryKey = 'classHolidayId';
    protected $keyType = 'string';
    public $incrementing = false;
    use HasFactory;
    protected $fillable = [
        'classHolidayId',
        'classId',
        'holidayId',
    ];
}
