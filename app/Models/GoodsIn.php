<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsIn extends Model
{
    protected $fillable = [
        'reference_number',
        'supplier_id',
        'product_id',
        'batch_code',
        'manufacture_date',
        'expiry_date',
        'quantity',
        'location_id',
        'notes',
        'status',
    ];

    protected $casts = [
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Relasi ke Supplier
    // penjelasan: ambil data supplier berdasarkan supplier_id pada tabel goods_in
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relasi ke Product
    // penjelasan: ambil data produk berdasarkan product_id pada tabel goods_in
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke Location
    // penjelasan: ambil data lokasi penyimpanan barang masuk berdasarkan location_id
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    //relasi ke batch
    //penjelasan: kirim data ke batches berdasarkan batch_code
    public function batch()
    {
        return $this->hasOne(Batch::class, 'batch_code', 'code');
    }

}
