<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Repair extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, InteractsWithMedia;

    protected $table = 'repairs';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->worksheet_no)) {
                $q->where('repairs.id', $request->worksheet_no);
            }
            if (!empty($request->repair_type)) {
                $q->where('repairs.repair_type', $request->repair_type);
            }
            if (!empty($request->contact)) {
                $q->where('repairs.contact', $request->contact);
            }
            if (!empty($request->status)) {
                $q->where('repairs.status', $request->status);
            }
            if (!empty($request->alert_date)) {
                $q->whereDate('repairs.repair_date', $request->alert_date);
            }
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function job()
    {
        return $this->morphTo();
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }


}
