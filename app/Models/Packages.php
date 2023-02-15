<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packages extends Model
{
    use HasFactory;
    protected $primaryKey = 'packageId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'packageId',
        'name',
        'startLevel',
        'endLevel',
        'activate',
    ];
}