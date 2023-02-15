<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    protected $fillable = [
        'adminId',
        'name',
        'email',
        'password',
    ];
    protected $primaryKey = 'adminId';
}
