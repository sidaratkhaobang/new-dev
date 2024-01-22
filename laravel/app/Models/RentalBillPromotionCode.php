<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalBillPromotionCode extends Model
{
    use HasFactory;
    protected $table = 'rental_bills_promotion_codes';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
