<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    //ijinkan field untuk diisi secara massal
    protected $fillable = [
        'product_id',
        'batch_code',
        'manufacture_date',
        'expiry_date',
        'quantity',
    ];

    //relasi dari model Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //relasi ke model StockMovement
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    //relasi ke model Stock
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    //relasi ke model barang keluar
    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }

}
