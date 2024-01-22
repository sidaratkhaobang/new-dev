<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Creator;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class BorrowCar extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;
    protected $table = 'borrow_cars';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public $sortableAs = ['worksheet_no','branch_id','transfer_branch_id','car_id'];

    public function scopeBranchFilter($query)
    {
        $user = Auth::user();
        return $query->where(function ($q) use ($user) {

            if ($user && $user->branch_id) {
                $q->where('borrow_cars.branch_id',$user->branch_id );
            } else {
                $q->whereRaw(STATUS_DEFAULT); // return an empty result set if not main branch
            }
        });
    }


    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->worksheet_no)) {
                $q->where('borrow_cars.id', 'like', $request->worksheet_no);
            }
            if (!empty($request->borrow_type)) {
                $q->where('borrow_cars.borrow_type', 'like', $request->borrow_type);
            }
            if (!empty($request->car_id)) {
                $q->where('borrow_cars.car_id', 'like', $request->car_id);
            }
            if (!empty($request->status)) {
                $q->where('borrow_cars.status', 'like', $request->status);
            }
            if (!empty($request->pickup_date_start) || !empty($request->pickup_date_end)) {
                $q->whereBetween('borrow_cars.start_date', [$request->pickup_date_start . " 00:00:00", $request->pickup_date_end . " 23:59:59"]);
            }
            if (!empty($request->return_date_start) || !empty($request->to_return_date_enddate)) {
                $q->whereBetween('borrow_cars.end_date', [$request->return_date_start . " 00:00:00", $request->return_date_end . " 23:59:59"]);
            }
            
            
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    public function drivingJob()
    {
        return $this->morphTo(DrivingJob::class ,'job');
    }

    public function borrowBranch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

}
