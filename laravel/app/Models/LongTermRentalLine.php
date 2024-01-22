<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;

class LongTermRentalLine extends Model
{
    use HasFactory, PrimaryUuid;

    protected $table = 'lt_rental_lines';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
    ];

    public function color()
    {
        return $this->hasOne(CarColor::class, 'id', 'car_color_id');
    }

    public function carClass()
    {
        return $this->hasOne(CarClass::class, 'id', 'car_class_id');
    }

    public function getRequestCarClassLine()
    {
        return $this->hasOne(RequestPremiumCarclassLine::class, 'lt_rental_line_id');
    }
}
