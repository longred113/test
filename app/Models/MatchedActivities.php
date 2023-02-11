<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchedActivities extends Model
{
    use HasFactory;
    protected $fillable = [
        'matchActivityId',
        'productId',
        'name',
        'type',
        'time',
    ];
}
