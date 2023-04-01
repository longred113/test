<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassTimeSlot extends Model
{
    use HasFactory;
    protected $primaryKey = 'classTimeSlotId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'classTimeSlotId',
        'name',
        'classStart',
        'classEnd',
    ];
}
