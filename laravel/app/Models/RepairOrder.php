<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RepairOrder extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, InteractsWithMedia;

    protected $table = 'repair_orders';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->order_worksheet_no)) {
                $q->where('repair_orders.id', $request->order_worksheet_no);
            }
            if (!empty($request->status)) {
                $q->where('repair_orders.status', $request->status);
            }
            if (!empty($request->center)) {
                $q->where('repair_orders.center_id', $request->center);
            }
        });
    }

    public function repair()
    {
        return $this->hasOne(Repair::class, 'id', 'repair_id');
    }

    public function repair_order_lines()
    {
        return $this->hasMany(RepairOrderLine::class, 'repair_order_id', 'id');
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    public function creditor()
    {
        return $this->hasOne(Creditor::class, 'id', 'center_id');
    }

    public function repair_order_date()
    {
        return $this->hasOne(RepairOrderDate::class, 'repair_order_id', 'id');
    }
}
