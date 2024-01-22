<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalProductAdditional extends Model
{
    use HasFactory, PrimaryUuid;
    protected $table = 'rental_product_additionals';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'rental_id',
        'product_additional_id',
        'car_id',
        'name',
        'price',
        'amount',
        'is_free',
        'is_from_product',
        'is_from_promotion',
        'outbound_is_check',
        'inbound_approve',
        'inbound_remark'
    ];


    public function product_additional(){
        return $this->hasOne(ProductAdditional::class, 'id', 'product_additional_id');
    }
}
