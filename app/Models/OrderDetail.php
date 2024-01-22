<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_id',
        'menu_name',
        'price',
        'qty',
        'total'
    ];

    protected $casts = [
        'order_id' => 'integer',
        'menu_id' => 'integer',
        'price' => 'integer',
        'qty' => 'integer',
        'total' => 'integer'
    ];
}
