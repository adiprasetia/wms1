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
}
