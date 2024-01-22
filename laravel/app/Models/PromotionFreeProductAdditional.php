<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionFreeProductAdditional extends Model
{
    use HasFactory;
    protected $table = 'promotions_free_product_additionals';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'promotion_id',
        'product_additional_id',
    ];

    public function product_additional()
    {
        return $this->hasOne(ProductAdditional::class, 'id', 'product_additional_id');
    }

    public function productAdditional()
    {
        return $this->hasOne(ProductAdditional::class, 'id', 'product_additional_id');
    }
}
