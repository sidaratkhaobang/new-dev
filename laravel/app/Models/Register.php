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
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Register extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;
    protected $table = 'registereds';
    public $incrementing = false;
    protected $keyType = 'string';
    // public $sortable = ['lot_no'];
    public $sortableAs = ['lot_no','car.engine_no','car.chassis_no'];


    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->lot_no_search)) {
                $q->where('registereds.lot_id', 'like', $request->lot_no_search);
            }
            if (!empty($request->car_class_search)) {
                $q->where('car_db.car_class_id', 'like', $request->car_class_search);
            }
            if (!empty($request->license_plate_search)) {
                $q->where('car_db.id', 'like', $request->license_plate_search);
            }
            if (!empty($request->status_search)) {
                $q->where('registereds.status', 'like', $request->status_search);
            }
        });
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class, 'id', 'po_id');
    }

    public function insurance()
    {
        return $this->hasOne(InsuranceLot::class, 'id', 'lot_id');
    }

    public function hirePurchase()
    {
        return $this->hasOne(HirePurchase::class, 'id', 'hire_purchase_id');
    }

    public function carCharacteristicTransport()
    {
        return $this->hasOne(CarCharacteristicTransport::class, 'id', 'car_characteristic_transport_id');
    }
}


