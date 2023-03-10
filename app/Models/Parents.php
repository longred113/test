<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    use HasFactory;
    protected $primaryKey = 'parentId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'parentId',
        'firstName',
        'lastName',
        'email',
        'phone',
        'studentIds',
    ];
}
