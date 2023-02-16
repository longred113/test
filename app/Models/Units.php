<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    use HasFactory;
    protected $primaryKey = 'unitId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'unitId',
        'matchedActivityId',
        'productId',
        'name',
        'startDate',
        'endDate',
    ]; 
}
