<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionFreeProduct extends Model
{
    use HasFactory;
    protected $table = 'promotions_free_products';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'promotion_id',
        'product_id',
    ];
}
