<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    //field field yang bisa diisi secara massal
    protected $fillable = [
        'code',
        'rack',
        'slot',
        'capacity',
    ];

    //relasi ke model Stock
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    //relasi ke model StockMovement
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    //relasi ke model GoodsIn
    public function goodsIns()
    {
        return $this->hasMany(GoodsIn::class);
    }

    //relasi ke model BarangKeluar
    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }

    // Auto-generate code from rack and slot
    protected static function booted(): void
    {
        static::saving(function (Location $location) {
            if (!empty($location->rack) && !empty($location->slot)) {
                $location->code = sprintf('%s-%s', $location->rack, $location->slot);
            }
        });
    }
}
