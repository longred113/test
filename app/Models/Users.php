<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;
    protected $primaryKey = 'userId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'userId',
        'name',
        'email',
        'password',
        'roleId',
        'teacherId',
        'studentId',
        'parentId',
        'campusManagerId',
        'activate',
    ];
}
