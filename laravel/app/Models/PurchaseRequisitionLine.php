<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\PrimaryUuid;

class PurchaseRequisitionLine extends Model
{
    use PrimaryUuid;

    protected $table = 'purchase_requisition_lines';
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
}
