<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionFreeCarClass extends Model
{
    use HasFactory;
    protected $table = 'promotions_free_car_classes';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'promotion_id',
        'car_class_id',
    ];

    public function carClass()
    {
        return $this->hasOne(CarClass::class, 'id', 'car_class_id');
    }
}
