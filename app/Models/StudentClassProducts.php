<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClassProducts extends Model
{
    use HasFactory;
    protected $primaryKey = 'studentClassProductId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'studentClassProductId',
        'studentId',
        'classProductId',
    ];
}
