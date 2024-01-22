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

class TaxRenewal extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;
    protected $table = 'tax_renewals';
    public $incrementing = false;
    protected $keyType = 'string';

    // public $sortable = ['lot_no'];
    public $sortableAs = ['lot_no','car.engine_no','car.chassis_no'];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            // if (!empty($s)) {
            //     $q->where('cars.engine_no', 'like', '%' . $s . '%');
            // }
            if (!empty($request->car_id)) {
                $q->where('tax_renewals.car_id', 'like', $request->car_id);
            }
            if (!empty($request->car_class)) {
                $q->where('car_classes.id', 'like', $request->car_class);
            }
            if (!empty($request->status)) {
                $q->where('tax_renewals.status', 'like', $request->status);
            }
            if (!empty($request->leasing)) {
                $q->where('cars.leasing_id', 'like', $request->leasing);
            }
        });
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    // public function hirePurchase()
    // {
    //     return $this->hasOne(HirePurchase::class, 'id', 'hire_purchase_id');
    // }
}
