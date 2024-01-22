<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Compensation extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;
    protected $table = 'compensations';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('compensations.worksheet_no', 'like', '%' . $s . '%');
            }
            if (!empty($request->compensation_id)) {
                $q->where('compensations.id', 'like', $request->compensation_id);
            }
            if (!empty($request->complaint_type)) {
                $q->where('compensations.type', 'like', $request->complaint_type);
            }
            if (!empty($request->status)) {
                $q->where('compensations.status', $request->status);
            }
            if (!empty($request->end_date)) {
                $q->where('compensations.end_date', $request->end_date);
            }
            if (!empty($request->accident_id)) {
                $q->where('compensations.accident_id', $request->accident_id);
            }
        });
    }

    public function insurer()
    {
        return $this->hasOne(InsuranceCompanies::class, 'id', 'insurer_parties_id');
    }

    public function carBrand()
    {
        return $this->hasOne(CarBrand::class, 'id', 'car_brand_parties_id');
    }

    public function claimBy()
    {
        return $this->hasOne(User::class, 'id', 'creator_id');
    }

    public function accident()
    {
        return $this->hasOne(Accident::class, 'id', 'accident_id');
    }
}