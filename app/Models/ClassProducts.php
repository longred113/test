<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassProducts extends Model
{
    use HasFactory;
    protected $primaryKey = 'classProductId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'classProductId',
        'productId',
        'classId',
        'status',
    ]; 
}
