<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Products extends Model
{
    use HasFactory;
     protected $primaryKey = 'productId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'productId',
        'packageId',
        'name',
        'startLevel',
        'endLevel',
        'details',
        'image',
        'activate',
    ];
}