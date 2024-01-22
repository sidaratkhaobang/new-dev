<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Illuminate\Support\Facades\Auth;
use Kyslik\ColumnSortable\Sortable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ReplacementCar extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, InteractsWithMedia, Sortable;
    protected $table = 'replacement_cars';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public $sortable = [
        'worksheet_no',
        'replacement_type',
        'job_type',
        'job_id',
        // 'place',
        'replacement_place',
        'replacement_date',
        'customer_name',
        'created_at',
    ];

    public function scopeBranchFilter($query)
    {
        $user = Auth::user();
        return $query->where(function ($q) use ($user) {

            if ($user && $user->branch_id) {
                $q->where('branch_id',$user->branch_id);
            } else {
                $q->whereRaw(STATUS_DEFAULT); // return an empty result set if not main branch
            }
        });
    }

    public function scopeSearch($query, $s, $request = null)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('replacement_cars.worksheet_no', 'like', '%' . $s . '%');
            }
            if (!empty($request->replacement_type)) {
                $q->where('replacement_cars.replacement_type', $request->replacement_type);
            }
            if (!empty($request->job_type)) {
                $q->where('replacement_cars.job_type', $request->job_type);
            }
            if (!empty($request->job_id)) {
                $q->where('replacement_cars.job_id', $request->job_id);
            }
            if (!empty($request->worksheet_id)) {
                $q->where('replacement_cars.id', $request->worksheet_id);
            }
            if (!empty($request->main_car_id)) {
                $q->where('replacement_cars.main_car_id', $request->main_car_id);
            }
            if (!empty($request->replacement_car_id)) {
                $q->where('replacement_cars.replacement_car_id', $request->replacement_car_id);
            }
        });
    }

    public function mainCar()
    {
        return $this->hasOne(Car::class, 'id', 'main_car_id')->withDefault();
    }

    public function replacementCar()
    {
        return $this->hasOne(Car::class, 'id', 'replacement_car_id')->withDefault();
    }

    public function drivingJob()
    {
        return $this->morphMany(DrivingJob::class ,'job');
    }
}