<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMatchedActivities extends Model
{
    use HasFactory;
    protected $primaryKey = 'productMatchedActivityId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'productMatchedActivityId',
        'productId',
        'productName',
        'matchedActivityId',
        'matchedActivityName',
    ];
}
