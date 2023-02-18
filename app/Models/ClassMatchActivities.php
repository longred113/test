<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassMatchActivities extends Model
{
    use HasFactory;
    // protected $primaryKey = 'classId';
    // protected $foreignKey = 'matchedActivityId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'classId',
        'matchedActivityId',
        'status',
    ];
}
