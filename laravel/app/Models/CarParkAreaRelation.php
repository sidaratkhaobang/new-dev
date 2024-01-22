<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarParkAreaRelation extends Model
{
    use HasFactory;

    protected $table = 'car_park_areas_relation';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'car_park_area_id',
        'car_group_id',
    ];
}
