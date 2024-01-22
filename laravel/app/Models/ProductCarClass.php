<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCarClass extends Model
{
    use HasFactory;
    protected $table = 'products_car_classes';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
