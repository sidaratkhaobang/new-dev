<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarParkArea extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus;

    protected $table = 'car_park_areas';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];


    public function carParkZone()
    {
        return $this->belongsTo(CarParkZone::class, 'car_park_zone_id');
    }
}
