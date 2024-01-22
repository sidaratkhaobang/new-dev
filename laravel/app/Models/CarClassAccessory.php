<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarClassAccessory extends Model
{

    protected $table = 'car_class_accessories';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;


    public function accessory()
    {
        return $this->hasOne(Accessories::class, 'id', 'accessory_id');
    }
    public function accessoryVersion()
    {
        return $this->hasOne(Accessories::class, 'id', 'accessory_version_id');
    }
}
