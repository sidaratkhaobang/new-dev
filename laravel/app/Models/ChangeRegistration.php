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

class ChangeRegistration extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;

    protected $table = 'change_registrations';
    public $incrementing = false;
    protected $keyType = 'string';
    // public $sortable = ['engine_no'];

    protected $fillable = [
        'id',
    ];
    public $sortableAs = ['car.engine_no', 'car.license_plate', 'hirePurchase.contract_no'];


    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            // if (!empty($s)) {
            //     $q->where('cars.engine_no', 'like', '%' . $s . '%');
            // }
            if (!empty($request->car_id)) {
                $q->where('change_registrations.car_id', 'like', $request->car_id);
            }
            if (!empty($request->request_id)) {
                $q->where('change_registrations.type', 'like', $request->request_id);
            }
            if (!empty($request->status)) {
                $q->where('change_registrations.status', 'like', $request->status);
            }
            if (!empty($request->leasing)) {
                $q->where('insurance_lots.leasing_id', 'like', $request->leasing);
            }
        });
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    public function hirePurchase()
    {
        return $this->hasOne(HirePurchase::class, 'id', 'hire_purchase_id');
    }

    public function Registered()
    {
        return $this->hasOne(Register::class, 'id', 'car_id');
    }
}
