<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;

class HirePurchase extends Model
{
    use HasFactory, PrimaryUuid, Creator, UpdateStatus;
    protected $table = 'hire_purchases';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];


    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }
    public function insurance_lot()
    {
        return $this->hasOne(InsuranceLot::class, 'id', 'lot_id');
    }
    public function purchase_order()
    {
        return $this->hasOne(PurchaseOrder::class, 'id', 'po_id');
    }

    public function importCarLine(){
        return $this->hasOne(ImportCarLine::class, 'id', 'car_id');
    }
}
