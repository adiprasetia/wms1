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

    //Relasi ke StockMovement
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    //Relasi ke Stock
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    //Relasi ke Batch
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    //Relasi ke BarangKeluar
    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }
}
