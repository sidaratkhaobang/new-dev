<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPriceCustomerGroup extends Model
{
    use HasFactory;
    protected $table = 'product_prices_customer_groups';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
