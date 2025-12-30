<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //mengijinkan pengisian massal untuk atribut-atribut berikut
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];
}
