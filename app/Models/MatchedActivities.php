<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchedActivities extends Model
{
    use HasFactory;
    protected $primaryKey = 'matchedActivityId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'matchedActivityId',
        'productId',
        'name',
        'time',
        'unitId',
        'type',
    ];
}
