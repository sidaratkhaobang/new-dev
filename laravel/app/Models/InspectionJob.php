<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class InspectionJob extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, UpdateStatus, Sortable, InteractsWithMedia, SoftDeletes;

    protected $table = 'inspection_jobs';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
    ];

    public $sortableAs = ['inspection_flow_name', 'rental_type', 'transfer_reason', 'license_plate', 'engine_no', 'zone_code', 'inspection_must_date', 'inspection_date', 'user_department_name', 'car_park_number', 'zone_code'];
    protected $hidden = ['media'];

    public function InspectionJobSteps()
    {
        return $this->hasMany(InspectionJobStep::class, 'inspection_job_id', 'id');
    }

    public function InspectionFlow()
    {
        return $this->hasMany(InspectionFlow::class, 'id', 'inspection_flow_id');
    }

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where(DB::raw("CONCAT(car_park_zones.code,car_parks.car_park_number)"), 'like', '%' . $s . '%');
                // $q->where('inspection_jobs.worksheet_no', 'like', '%' . $s . '%');
                // $q->orWhere('cars.license_plate', 'like', '%' . $s . '%');
                // $q->orWhere('cars.engine_no', 'like', '%' . $s . '%');
            }
            if (!empty($request->worksheet_no)) {
                $q->where('inspection_jobs.id', 'like', $request->worksheet_no);
            }
            if (!empty($request->inspection_form)) {
                $q->where('inspection_flows.id', 'like', $request->inspection_form);
            }
            if (!empty($request->inspection_must_date)) {
                $q->where('inspection_jobs.inspection_must_date', 'like', $request->inspection_must_date);
            }
            if (!empty($request->car_park_zone)) {
                $q->where('car_park_zones.id', 'like', $request->car_park_zone);
            }
            if (!empty($request->car_id)) {
                $q->where('cars.id', 'like', $request->car_id);
            }
            if (!empty($request->status)) {
                $q->where('inspection_jobs.inspection_status', 'like', $request->status);
            }
        });
    }

    public function rental()
    {
        return $this->morphTo();
    }
}
