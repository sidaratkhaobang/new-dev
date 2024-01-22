<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;

class DealerCheckCar extends Model
{
    use HasFactory,  PrimaryUuid;
    protected $table = 'dealer_check_cars';
    // public $incrementing = false;
    protected $keyType = 'string';
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

    public function Creditor()
    {
        return $this->hasOne(Creditor::class, 'id', 'dealer_id');
    }

}
