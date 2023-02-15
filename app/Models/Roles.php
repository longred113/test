<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;
    protected $primaryKey = 'roleId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'roleId',
        'name',
        'activate',
    ];
}
