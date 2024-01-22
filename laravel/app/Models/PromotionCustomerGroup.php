<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionCustomerGroup extends Model
{
    use HasFactory;
    protected $table = 'promotions_customer_groups';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'promotion_id',
        'customer_group_id',
    ];
}
