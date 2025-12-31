<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'date',
        'product_id',
        'batch_id',
        'location_id',
        'type',
        'quantity',
        'reference',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke Batch
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    // Relasi ke Location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
