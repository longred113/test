<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductEnrollments extends Model
{
    use HasFactory;
    protected $primaryKey = 'productEnrollmentId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'productEnrollmentId',
        'productId',
        'enrollmentId',
        'date',
    ];
}
