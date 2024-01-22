<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class OwnershipTransfer extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;
    protected $table = 'ownership_transfers';
    public $incrementing = false;
    protected $keyType = 'string';
    public $sortableAs = ['car.car_class','car.engine_no','car.chassis_no','hirePurchase.contract_no','car.license_plate'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('cars_db.engine_no', 'like', '%' . $s . '%');
            }
            if (!empty($request->actual_last_payment_date_search)) {
                $q->where('hire_purchases_db.actual_last_payment_date', 'like', $request->actual_last_payment_date_search);
            }
            if (!empty($request->contract_no_search)) {
                $q->where('hire_purchases_db.contract_no', 'like', $request->contract_no_search);
            }
            if (!empty($request->status_search)) {
                $q->where('ownership_transfers.status', 'like', $request->status_search);
            }
            if (!empty($request->leasing_search)) {
                $q->where('insurance_lots_db.leasing_id', 'like', $request->leasing_search);
            }
        });
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    // public function purchaseOrder()
    // {
    //     return $this->hasOne(PurchaseOrder::class, 'id', 'po_id');
    // }

    public function insurance()
    {
        return $this->hasOne(InsuranceLot::class, 'id', 'lot_id');
    }

    public function hirePurchase()
    {
        return $this->hasOne(HirePurchase::class, 'id', 'hire_purchase_id');
    }
}
