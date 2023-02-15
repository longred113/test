<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;
    protected $primaryKey = 'campusId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'campusId',
        'name',
        'indicated',
        'contact',
        'activate',
    ];
}
