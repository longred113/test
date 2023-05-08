<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassTimes extends Model
{
    protected $primaryKey = 'classTimeId';
    protected $keyType = 'string';
    public $incrementing = false;
    use HasFactory;
    protected $fillable = [
        'classTimeId',
        'classId',
        'day',
        'classTimeSlot',
        'classStartDate',
        'classEndDate',
    ];
}
