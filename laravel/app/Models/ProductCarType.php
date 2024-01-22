<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCarType extends Model
{
    use HasFactory;
    protected $table = 'products_car_types';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
