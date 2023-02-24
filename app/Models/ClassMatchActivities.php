<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassMatchActivities extends Model
{
    use HasFactory;
    protected $primaryKey = 'classMatchActivityId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'classMatchActivityId',
        'classId',
        'matchedActivityId',
        'status',
    ];
}
