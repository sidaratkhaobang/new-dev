<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstallEquipmentPurchaseOrder extends Model
{
    use HasFactory, PrimaryUuid, UpdateStatus, Creator, SoftDeletes;
    use Sortable;

    protected $table = 'install_equipment_purchase_orders';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['worksheet_no'];
    public $sortableAs = ['worksheet_no', 'ie_worksheet_no', 'supplier_name'];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->install_equipment_po_no)) {
                $q->where('install_equipment_purchase_orders.id', $request->install_equipment_po_no);
            }
            if (!empty($request->install_equipment_no)) {
                $q->where('install_equipment_purchase_orders.install_equipment_id', $request->install_equipment_no);
            }
            if (!empty($request->supplier_id)) {
                $q->where('install_equipment_purchase_orders.supplier_id', $request->supplier_id);
            }
            if (!empty($request->status_id)) {
                $q->where('install_equipment_purchase_orders.status', $request->status_id);
            }
            if (!empty($request->chassis_no)) {
                $q->where('install_equipment_purchase_orders.car_id', $request->chassis_no);
            }
            if (!empty($request->license_plate)) {
                $q->where('install_equipment_purchase_orders.car_id', $request->license_plate);
            }
        });
    }

    // public function createdBy()
    // {
    //     return $this->belongsTo(User::class, 'created_by');
    // }

    public function supplier()
    {
        return $this->hasOne(Creditor::class, 'id', 'supplier_id');
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    public function install_equipment()
    {
        return $this->hasOne(InstallEquipment::class, 'id', 'install_equipment_id');
    }

    public function drivingJob()
    {
        return $this->morphTo(DrivingJob::class, 'job');
    }

    public function installEquipmentPoLines()
    {
        return $this->hasMany(InstallEquipmentPOLine::class, 'install_equipment_po_id', 'id');

    }
}
