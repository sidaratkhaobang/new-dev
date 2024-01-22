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
use Kyslik\ColumnSortable\Sortable;

class RequestReceipt extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;
    protected $table = 'request_receipts';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public $sortable = ['worksheet_no', 'type', 'created_at', 'status'];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->type)) {
                $q->where('type', $request->type);
            }
            if (!empty($request->status)) {
                $q->where('status', $request->status);
            }
        });
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function province()
    {
        return $this->hasOne(Province::class, 'id', 'customer_province_id');
    }


    public function district()
    {
        return $this->hasOne(Amphure::class, 'id', 'customer_district_id');
    }


    public function subDistrict()
    {
        return $this->hasOne(District::class, 'id', 'customer_subdistrict_id');
    }
}