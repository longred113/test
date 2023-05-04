<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGroups extends Model
{
    protected $primaryKey = 'productGroupId';
    protected $keyType = 'string';
    public $incrementing = false;
    use HasFactory;
    protected $fillable = [
        'productGroupId',
        'groupId',
        'groupName',
        'productId',
        'productName',
    ];
}
