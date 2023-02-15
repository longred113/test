<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    protected $primaryKey = 'adminId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'adminId',
        'name',
        'email',
        'password',
    ];
}
