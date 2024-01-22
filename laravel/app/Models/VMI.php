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
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class VMI extends Model implements Auditable
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    use AuditableTrait;

    public $incrementing = false;
    public $sortable = [
        'worksheet_no',
        'lot_number',
    ];
    protected $table = 'voluntary_motor_insurances';
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

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($query) use ($request) {
        });
    }

    public function scopeInsuranceAvailable($query)
    {
        $query = $query->where('status', InsuranceStatusEnum::COMPLETE)
            ->where('status_vmi', InsuranceCarStatusEnum::UNDER_POLICY)
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
