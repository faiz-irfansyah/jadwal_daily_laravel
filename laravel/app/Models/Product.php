<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
        /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'image',
        'seri',
        'merk',
        'sistem',
        'ukuran',
        'kamera_belakang',
        'kamera_depan',
        'price',
        'stock',
    ];
}
