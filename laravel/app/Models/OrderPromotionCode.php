<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;

class OrderPromotionCode extends Model
{
    use HasFactory, PrimaryUuid;

    protected $table = 'order_promotion_codes';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
    ];
}
