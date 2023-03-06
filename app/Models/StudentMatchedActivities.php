<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentMatchedActivities extends Model
{
    use HasFactory;
    protected $primaryKey = 'studentMatchedActivityId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'studentMatchedActivityId',
        'studentId',
        'matchedActivityId',
        'name',
        'status',
    ];
}
