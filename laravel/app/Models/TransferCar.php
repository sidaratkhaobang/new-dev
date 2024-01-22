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

class TransferCar extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;

    protected $table = 'transfer_cars';
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
                $q->where('transfer_cars.branch_id',$user->branch_id );
            } else {
                $q->whereRaw(STATUS_DEFAULT); // return an empty result set if not main branch
            }
        });
    }

    public function scopeTransferBranchFilter($query)
    {
        $user = Auth::user();
        return $query->where(function ($q) use ($user) {

            if ($user && $user->branch_id) {
                $q->where('transfer_branch_id',$user->branch_id );
            } else {
                $q->whereRaw(STATUS_DEFAULT); // return an empty result set if not main branch
            }
        });
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function branchTransfer()
    {
        return $this->hasOne(Branch::class, 'id', 'transfer_branch_id');
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    public function userConfirm()
    {
        return $this->hasOne(User::class, 'id', 'confirmation_user_id');
    }

    public function userConfirmPickup()
    {
        return $this->hasOne(User::class, 'id', 'pick_up_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function drivingJob()
    {
        return $this->morphMany(DrivingJob::class ,'job');
    }

    // public function carClass()
    // {
    //     return $this->hasOne(CarClass::class, 'id', 'car_id');
    // }

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->from_branch_id)) {
                // dd($request->from_branch_id);
                $q->where('transfer_cars.branch_id', 'like', $request->from_branch_id);
            }
            if (!empty($request->to_branch_id)) {
                $q->where('transfer_cars.transfer_branch_id', 'like', $request->to_branch_id);
            }
            if (!empty($request->status)) {
                $q->where('transfer_cars.status', 'like', $request->status);
            }
            if (!empty($request->from_date) || !empty($request->to_date)) {
                $q->whereBetween('transfer_cars.delivery_date', [$request->from_date . " 00:00:00", $request->to_date . " 23:59:59"]);
            }
            
            
        });
    }

}
