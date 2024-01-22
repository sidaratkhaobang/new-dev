<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPriceCarClass extends Model
{
    use HasFactory;
    protected $table = 'product_prices_car_classes';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
