<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPackages extends Model
{
    use HasFactory;
    protected $primaryKey = 'productPackageId';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'productPackageId',
        'productId',
        'packageId',
        'status',
    ]; 
}
