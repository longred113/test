<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassMaterials extends Model
{
    use HasFactory;
    protected $primaryKey = 'classMaterialId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'classMaterialId',
        'writer',
        'class',
        'title',
        'view',
        'date',
    ];
}
