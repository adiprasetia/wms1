<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //Ijinkan fields dari formuntuk diisi secara massal
    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'unit',
        'category',
        'status',
    ];
}
