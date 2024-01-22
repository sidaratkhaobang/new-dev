<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPriceDestination extends Model
{
    use HasFactory;
    protected $table = 'product_prices_destinations';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
