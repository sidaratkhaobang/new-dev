<?php

namespace App\Models;

use App\Enums\InsuranceCarStatusEnum;
use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Accident extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, Sortable, UpdateStatus, InteractsWithMedia;

    public $incrementing = false;
    public $sortable = ['accident_type', 'case', 'status'];
    // public $timestamps = false;
    public $sortableAs = ['worksheet_no', 'car.license_plate', 'car.chassis_no', 'claim_no', 'accident_date'];
    protected $table = 'accidents';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->worksheet)) {
                $q->where('accidents.id', 'like', $request->worksheet);
            }
            if (!empty($request->accident_type)) {
                $q->where('accidents.accident_type', 'like', $request->accident_type);
            }
            if (!empty($request->license_plate)) {
                $q->where('accidents.car_id', 'like', $request->license_plate);
            }
            if (!empty($request->status)) {
                $q->where('accidents.status', 'like', $request->status);
            }


        });
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    public function carBrand()
    {
        return $this->hasOne(CarBrand::class, 'id', 'car_brand_id');
    }

    public function province()
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }


    public function district()
    {
        return $this->hasOne(Amphure::class, 'id', 'district_id');
    }


    public function subDistrict()
    {
        return $this->hasOne(District::class, 'id', 'subdistrict_id');
    }

    public function claimlines()
    {
        return $this->hasMany(Car::class, 'id', 'accident_id');
    }

    public function vmiUnderPolicy()
    {
        return $this->hasOne(VMI::class, 'car_id', 'car_id')->where('status_vmi', InsuranceCarStatusEnum::UNDER_POLICY)->latestOfMany();
    }

    public function rental()
    {
        return $this->belongsTo($this->job_type, 'job_id', 'id');
    }
}
