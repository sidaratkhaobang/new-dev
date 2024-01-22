<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstallEquipment extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, UpdateStatus, Creator, SoftDeletes, InteractsWithMedia;
    protected $table = 'install_equipments';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->install_equipment_no)) {
                $q->where('install_equipments.id', $request->install_equipment_no);
            }
            if (!empty($request->purchase_order_no)) {
                $q->where('install_equipments.po_id', $request->purchase_order_no);
            }
            if (!empty($request->supplier_id)) {
                $q->where('install_equipments.supplier_id', $request->supplier_id);
            }
            if (!empty($request->install_equipment_po_no)) {
                $q->where('install_equipments.id', $request->install_equipment_po_no);
            }
            if (!empty($request->create_date)) {
                $q->whereDate('install_equipments.created_at', $request->create_date);
            }
            if (!empty($request->chassis_no)) {
                $q->where('install_equipments.car_id', $request->chassis_no);
            }
            if (!empty($request->license_plate)) {
                $q->where('install_equipments.car_id', $request->license_plate);
            }
            if (!empty($request->status_id)) {
                $q->where('install_equipments.status', $request->status_id);
            }
            if (!empty($request->lot_no)) {
                $q->where('install_equipments.lot_no', $request->lot_no);
            }
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function supplier()
    {
        return $this->hasOne(Creditor::class, 'id', 'supplier_id');
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class, 'id', 'po_id');
    }

    public function install_equipment_po()
    {
        return $this->hasOne(InstallEquipmentPurchaseOrder::class, 'install_equipment_id');
    }

    public function drivingJob()
    {
        return $this->morphMany(DrivingJob::class ,'job');
    }
}
