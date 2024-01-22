<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\CarCategory;
use App\Models\CarBrand;


class CarType extends Model
{

    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'car_types';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'code',
        'car_category_id',
        'car_brand_id',
        'car_group_id',
    ];

    public $sortable = ['code', 'name', 'car_category_id', 'car_brand_id', 'car_group_id'];
    // public $appends = [
    //     'car_brands.name'
    // ];
    public $sortableAs = ['car_brand_name', 'car_category_name', 'car_group_name'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where(function ($q2) use ($s, $request) {
                    $q2->where('car_types.code', 'like', '%' . $s . '%');
                    $q2->orWhere('car_types.name', 'like', '%' . $s . '%');
                });
            }
            // if (!empty($request->category_id)) {
            //     $q->where('car_category_id', $request->category_id);
            // }
            // if (!empty($request->brand_id)) {
            //     $q->Where('car_brand_id', $request->brand_id);
            // }
            if (!empty($request->category_id)) {
                $q->where('car_categories.id', $request->category_id);
            }
            if (!empty($request->brand_id)) {
                $q->Where('car_brands.id', $request->brand_id);
            }
            if (!empty($request->group_id)) {
                $q->Where('car_groups.id', $request->group_id);
            }
        });
    }

    public function car_category()
    {
        return $this->hasOne(CarCategory::class, 'id', 'car_category_id');
    }

    public function car_brand()
    {
        return $this->hasOne(CarBrand::class, 'id', 'car_brand_id');
    }

    public function carGroup()
    {
        return $this->hasOne(CarGroup::class, 'id', 'car_group_id');
    }

    public function car_group()
    {
        return $this->hasOne(CarGroup::class, 'id', 'car_group_id');

        //     public function carBrand()
        //     {
        //         return $this->belongsTo(CarBrand::class, 'car_brand_id');
        //     }

        //     public function carCategory()
        //     {
        //         return $this->belongsTo(CarCategory::class, 'car_category_id');
        // >>>>>>> 531d900d96bb6e7ce152b4c9c625e97cb0072818
        //     }
    }
}
