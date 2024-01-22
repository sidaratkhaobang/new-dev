<?php

namespace App\Models;

use App\Models\CarPart;
use App\Models\CarType;
use App\Models\Traits\Creator;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CarClass extends Model implements HasMedia
{
    use HasFactory,  PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;

    protected $table = 'car_classes';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['full_name', 'name', 'car_type_id', 'engine_size', 'manufacturing_year', 'remark', 'gear_id'];

    public $sortableAs = ['car_brand_name'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where(function ($q2) use ($s,$request){
                $q2->where('car_classes.full_name', 'like', '%' . $s . '%');
                $q2->orWhere('car_classes.name', 'like', '%' . $s . '%');
            });
            }
            // if (!empty($request->name)) {
            //     $q->orWhere('car_classes.id', $request->name);
            // }
            // if (!empty($request->full_name)) {
            //     $q->orWhere('car_classes.id', $request->full_name);
            // }
            if (!empty($request->engine)) {
                $q->where('car_classes.engine_size', $request->engine);
            }
            if (!empty($request->year)) {
                $q->where('car_classes.manufacturing_year', $request->year);
            }
            if (!empty($request->gear_id)) {
                $q->where('car_classes.gear_id', $request->gear_id);
            }
            if (!empty($request->car_brand_id)) {
                $q->where('car_types.car_brand_id', $request->car_brand_id);
            }
            if (!empty($request->car_type_id)) {
                $q->where('car_classes.car_type_id', $request->car_type_id);
            }
        });
    }

    public function carPartGear()
    {
        return $this->belongsTo(CarPart::class, 'gear_id');
    }

    public function carType()
    {
        return $this->belongsTo(CarType::class, 'car_type_id');
    }

    /* public function registerMediaConversions(Media $media = null): void
    {
        if (strcmp(env('APP_ENV'), 'local') == 0) {
            $this->addMediaConversion('thumb')->fit(Manipulations::FIT_FILL, 100, 100)->background('ffffff')->nonQueued();
        } else {
            $this->addMediaConversion('thumb')->fit(Manipulations::FIT_FILL, 100, 100)->background('ffffff');
        }
    } */
}
