<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class DrivingJob extends Model implements Auditable
{
    use HasFactory,  PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    use AuditableTrait;

    protected $table = 'driving_jobs';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        // 'id',
        // 'driver_id',
        // 'est_distance',
        // 'start_date',
        // 'end_date',
        // 'estimate_prepare_date',
        // 'estimate_start_date',
        // 'estimate_end_job_date',
        // 'estimate_arrive_date',
        // 'estimate_end_date',
        // 'atk_check',
        // 'alcohol_check',
        // 'alcohol',
    ];
    public $guarded = ['id', 'estimate_rented_date'];

    public $sortable = ['worksheet_no', 'job_id', 'job_type', 'created_at', 'is_confirm_wage', 'status', 'name', 'income'];
    public $sortableAs = ['driver_name', 'work_day'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('driving_jobs.worksheet_no', 'like', '%' . $s . '%');
                $q->orWhere('driving_jobs.driver_name', 'like', '%' . $s . '%');
            }
            if (!empty($request->worksheet_no)) {
                $q->where('driving_jobs.id', $request->worksheet_no);
            }
            if (!empty($request->work_status)) {
                $q->where('driving_jobs.status', $request->work_status);
            }
            // if (!empty($request->job_type)) {
            //     // dd($request->job_type);
            //     $q->where('driving_jobs.job_type', $request->job_type);
            // }
            if (!empty($request->driver_id)) {
                $q->where('driving_jobs.driver_id', $request->driver_id);
            }
        });
    }

    public function getRefStartDateAttrAttribute($value)
    {
        if ($this->job_type == Rental::class) {
            return $this->job?->pickup_date;
        } else if ($this->job_type == LongTermRental::class) {
            return $this->job?->contract_start_date;
        } else if ($this->job_type == ImportCarLine::class) {
            return $this->job?->delivery_date;
        } else if ($this->job_type == TransferCar::class) {
            return $this->job?->delivery_date;
        } else {
            return null;
        }
    }

    public function getRefEndDateAttrAttribute($value)
    {
        if ($this->job_type == Rental::class) {
            return $this->job?->return_date;
        } else if ($this->job_type == LongTermRental::class) {
            return $this->job?->contract_end_date;
        } else if ($this->job_type == ImportCarLine::class) {
            return null;
        } else if ($this->job_type == TransferCar::class) {
            return null;
        } else {
            return null;
        }
    }

    public function getRefWorksheetNoAttrAttribute($value)
    {
        if ($this->job_type == Rental::class) {
            return $this->job?->worksheet_no;
        } else if ($this->job_type == LongTermRental::class) {
            return $this->job?->worksheet_no;
        } else if ($this->job_type == ImportCarLine::class) {
            return $this->job?->importCar?->purchaseOrder?->po_no;
        } else if ($this->job_type == TransferCar::class) {
            return $this->job?->worksheet_no;
        } else {
            return null;
        }
    }

    public function job()
    {
        return $this->morphTo();
    }

    public function driver()
    {
        return $this->hasOne(Driver::class, 'id', 'driver_id');
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    public function carParkTransfer()
    {
        return $this->belongsTo(CarParkTransfer::class, 'id', 'driving_job_id');
    }
}
