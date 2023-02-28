<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProducts extends Model
{
    use HasFactory;
    protected $primaryKey = 'studentProductId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'studentProductId',
        'studentId',
        'productId',
    ];
}
