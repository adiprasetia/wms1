<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    //ijinkan isi model sesuai kebutuhan aplikasi Anda
    protected $fillable = [
        'kode_barang_keluar',
        'tanggal_keluar',
        'customer_id',
        'product_id',
        'stock_id', //ganti ke batch_id
        'quantity',
        'location_id',
        'keterangan',
    ];

    //relasi ke model Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    //relasi ke model Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    //relasi ke model Stock
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
    //relasi ke model Location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    //relasi ke model Batch
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

}

