<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Model;


class LongTermRentalMonth extends Model
{
    use HasFactory, PrimaryUuid;

    protected $table = 'lt_rental_month';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
    ];

    public function getRequestPremiumPrice()
    {
        return $this->hasOne(RequestPremiumPrice::class, 'lt_rental_month_id');
    }
}
