<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    //ijinkan field untuk diisi secara massal
    protected $fillable = [
        'product_id',
        'batch_id',
        'location_id',
        'quantity',
    ];

    //relasi ke model Batch
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    //relasi ke model Location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    //relasi ke model Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
