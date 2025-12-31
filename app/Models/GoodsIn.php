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
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke Location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Relasi ke Batch
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    // Relasi ke Stock
    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    // Relasi ke StockMovement
    public function stockMovement()
    {
        return $this->hasOne(StockMovement::class, 'reference');
    }
}
