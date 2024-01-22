<?php

namespace App\Models;

use App\Enums\InsuranceCarStatusEnum;
use App\Enums\InsuranceStatusEnum;
use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class CMI extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    protected $table = 'compulsory_motor_insurances';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'worksheet_no',
        'year',
        'type',
        'job_id',
        'job_type',
        'car_id',
        'registration_type',
        'lot_id',
        'lot_number',
        'status'
    ];

    public $sortable = [
        'worksheet_no',
        'lot_number',
    ];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($query) use ($request) {
        });
    }
    public function scopeInsuranceAvailable($query)
    {
        $query = $query->where('status', InsuranceStatusEnum::COMPLETE)
            ->where('status_cmi', InsuranceCarStatusEnum::UNDER_POLICY)
            ->whereDate('term_end_date', '>', Carbon::now());
        return $query;
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    public function insuranceLot()
    {
        return $this->hasOne(InsuranceLot::class, 'id', 'lot_id');
    }

    public function insurer()
    {
        return $this->hasOne(InsuranceCompanies::class, 'id', 'insurer_id');
    }

    public function job()
    {
        return $this->morphTo();
    }
}
