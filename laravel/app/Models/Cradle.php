<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Creator;

class Cradle extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    protected $table = 'cradles';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public $sortable = ['name','cradle_tel','cradle_type','is_onsite_service','status'];
    public $sortableAs = ['cradle_types','Province.name_th'];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($query) use ($request) {
            if (!empty($request->garage)) {
                $query->where('cradles.id', $request->garage);
            }
            if (!empty($request->garage_type)) {
                $query->where('cradles.cradle_type', $request->garage_type);
            }
            if (!empty($request->province_id)) {
                $query->where('cradles.province', $request->province_id);
            }
            if (isset($request->status)) {
                $query->where('cradles.status', $request->status);
            }
        });
    }

    public function Province()
    {
        return $this->hasOne(Province::class, 'id', 'province');
    }


    public function District()
    {
        return $this->hasOne(Amphure::class, 'id', 'district');
    }


    public function SubDistrict()
    {
        return $this->hasOne(District::class, 'id', 'subdistrict');
    }

    public function zipCode()
    {
        return $this->hasOne(District::class, 'id', 'subdistrict');
    }
}
