<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //mengijinkan pengisian massal untuk atribut-atribut berikut
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    //relasi ke model GoodsIn
    public function goodsIns()
    {
        return $this->hasMany(GoodsIn::class);
    }
}
