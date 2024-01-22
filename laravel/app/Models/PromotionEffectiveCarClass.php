<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionEffectiveCarClass extends Model
{
    use HasFactory;
    protected $table = 'promotions_effective_car_classes';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'promotion_id',
        'car_class_id',
    ];
}
