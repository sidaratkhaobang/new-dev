<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use App\Models\Traits\Creator;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetCar extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus;

    protected $table = 'asset_cars';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($request->lot_id)) {
                $q->where('asset_cars.lot_id', $request->lot_id);
            }

            if (!empty($request->car_id)) {
                $q->where('asset_cars.car_id', $request->car_id);
            }

            if (!empty($request->po_id)) {
                $q->where('asset_cars.po_id', $request->po_id);
            }

            if (!empty($request->hire_purchase_id)) {
                $q->where('asset_cars.hire_purchase_id', $request->hire_purchase_id);
            }

            if (!empty($request->registered_id)) {
                $q->where('asset_cars.registered_id', $request->registered_id);
            }

            if (!empty($request->status)) {
                $q->where('asset_cars.status', $request->status);
            }
        });
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    public function insuranceLot()
    {
        return $this->hasOne(InsuranceLot::class, 'id', 'lot_id');
    }

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class, 'id', 'po_id');
    }

    public function hirePurchase()
    {
        return $this->hasOne(HirePurchase::class, 'id', 'hire_purchase_id');
    }

    public function register()
    {
        return $this->hasOne(Register::class, 'id', 'registered_id');
    }
}
