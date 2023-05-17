<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holidays extends Model
{
    protected $primaryKey = 'holidayId';
    protected $keyType = 'string';
    public $incrementing = false;
    use HasFactory;
    protected $fillable = [
        'holidayId',
        'name',
        'timeZone',
        'startDate',
        'endDate',
        'duration',
    ];
}
