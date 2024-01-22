<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarClassColor extends Model
{
    protected $table = 'car_class_colors';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function color()
    {
        return $this->hasOne(CarColor::class, 'id', 'car_color_id');
    }
}
