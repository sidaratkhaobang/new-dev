<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarParkTransfer extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'car_park_transfers';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public $sortable = ['worksheet_no', 'transfer_type', 'est_transfer_date', 'start_date'];
    public $sortableAs = ['license_plate', 'engine_no', 'chassis_no', 'car_status_name', 'transfer_date', 'car_categories_name'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where(function ($q2) use ($s) {
                    $q2->where('car_park_transfers.worksheet_no', 'like', '%' . $s . '%');
                    $q2->orWhere('cars.license_plate', 'like', '%' . $s . '%');
                    $q2->orWhere('cars.engine_no', 'like', '%' . $s . '%');
                    $q2->orWhere('cars.chassis_no', 'like', '%' . $s . '%');
                    $q2->orWhere('car_statuses.name', 'like', '%' . $s . '%');
                });
            }
            if (!empty($request->status)) {
                $q->where('car_park_transfers.status', $request->status);
            }
            if (!empty($request->est_transfer_date)) {
                $q->where('car_park_transfers.est_transfer_date', $request->est_transfer_date);
            }
            // if (!empty($request->start_date)) {
            //     $q->where('car_park_transfers.start_date', $request->start_date);
            // }
            // if (!empty($request->end_date)) {
            //     $q->where('car_park_transfers.end_date', $request->end_date);
            // }
            if (!empty($request->start_date) && !empty($request->end_date)) {
                $q->whereDate('car_park_transfers.start_date', '>=', $request->start_date)->whereDate('car_park_transfers.end_date', '<=', $request->end_date);
            }
            if (!empty($request->transfer_type)) {
                $q->where('car_park_transfers.transfer_type', $request->transfer_type);
            }
            if (!empty($request->car_id) || !empty($request->engine_no) || !empty($request->chassis_no)) {
                $q->whereIn('car_park_transfers.car_id', [$request->car_id, $request->engine_no, $request->chassis_no]);
            }
        });
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function carparktransferlog()
    {
        return $this->hasOne(CarParkTransferLog::class, 'car_park_transfer_id', 'id');
    }

    public function carPark()
    {
        return $this->belongsTo(CarPark::class, 'car_park_id');
    }

    public function driving_job()
    {
        return $this->belongsTo(DrivingJob::class, 'driving_job_id');
    }

    public function drivingJob()
    {
        return $this->belongsTo(DrivingJob::class);
    }
}
