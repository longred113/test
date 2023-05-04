<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblGroups extends Model
{
    protected $primaryKey = 'groupId';
    protected $keyType = 'string';
    public $incrementing = false;
    use HasFactory;
    protected $fillable = [
        'groupId',
        'name',
    ];
}
