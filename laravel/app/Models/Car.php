<?php

namespace App\Models;

use App\Enums\BorrowCarEnum;
use App\Enums\CarEnum;
use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\Route;

class Car extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;

    protected $table = 'cars';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['engine_no', 'chassis_no', 'license_plate', 'created_at'];
    public $sortableAs = ['class_name', 'zone_code', 'car_park_number', 'slot'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($request->engine_no)) {
                $q->where('cars.id', 'like', '%' . $request->engine_no . '%');
            }
            if (!empty($request->chassis_no)) {
                $q->where('cars.id', 'like', '%' . $request->chassis_no . '%');
            }
            if (!empty($request->license_plate)) {
                $q->where('cars.id', 'like', '%' . $request->license_plate . '%');
            }
            if (!empty($request->rental_type)) {
                $q->where('cars.rental_type', 'like', '%' . $request->rental_type . '%');
            }
            if (!empty($request->storage_location)) {
                $q->where('cars.car_park', 'like', '%' . $request->storage_location . '%');
            }
            if (!empty($request->status)) {
                $q->where('cars.status', 'like', '%' . $request->status . '%');
            }
        });
    }

    function getImageUrlAttribute()
    {
        $medias = $this->getMedia('car_images');
        $files = get_medias_detail($medias);
        return isset($files[0]['url']) ? $files[0]['url'] : asset('images/car-sample/car-placeholder.png');
    }

    public function carColor()
    {
        return $this->hasOne(CarColor::class, 'id', 'car_color_id');
    }

    public function carGroup()
    {
        return $this->hasOne(CarGroup::class, 'id', 'car_group_id');
    }

    // for category use
    // public function carCategory()
    // {
    //     return $this->hasOne(CarCategory::class, 'id', 'car_categorie_id');
    // }

    public function carClass()
    {
        return $this->hasOne(CarClass::class, 'id', 'car_class_id');
    }

    public function carCategory()
    {
        return $this->hasOne(CarCategory::class, 'id', 'car_category_id');
    }

    public function carCharacteristic()
    {
        return $this->hasOne(CarCharacteristic::class, 'id', 'car_characteristic_id');
    }

    public function carPartGear()
    {
        return $this->hasOne(CarPart::class, 'id', 'gear_id');
    }

    public function carTire()
    {
        return $this->hasOne(CarTire::class, 'id', 'car_tire_id');
    }

    public function carBrand()
    {
        return $this->hasOne(CarBrand::class, 'id', 'car_brand_id');
    }

    public function carAccessory()
    {
        return $this->hasMany(CarBrand::class, 'car_id', 'car_accessories');
    }
    public function inspection_job()
    {
        return $this->hasMany(InspectionJob::class, 'car_id', 'id');
    }

    public function cmi()
    {
        return $this->hasMany(CMI::class, 'car_id', 'id');
    }

    public function vmi()
    {
        return $this->hasMany(VMI::class, 'car_id', 'id');
    }

    public function accessories()
    {
        return $this->hasMany(CarAccessory::class, 'car_id', 'id');
    }

    public function rental_lines()
    {
        return $this->hasMany(RentalLine::class, 'car_id', 'id');
    }

    public function register()
    {
        return $this->hasOne(Register::class, 'car_id', 'id');
    }

    public function creditor()
    {
        return $this->hasOne(Creditor::class, 'id', 'leasing_id');
    }
}
