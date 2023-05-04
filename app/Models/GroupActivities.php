<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupActivities extends Model
{
    protected $primaryKey = 'groupActivityId';
    protected $keyType = 'string';
    public $incrementing = false;
    use HasFactory;
    protected $fillable = [
        'groupActivityId',
        'groupId',
        'groupName',
        'matchedActivityId',
        'matchedActivityName',
    ];
}
