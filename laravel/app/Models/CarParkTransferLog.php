<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarParkTransferLog extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    protected $table = 'car_park_transfer_logs';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
    public $sortable = ['transfer_date','transfer_type'];
    public $sortableAs = ['license_plate', 'engine_no', 'chassis_no', 'car_status_name', 'transfer_date','est_transfer_date','fullname','date_start','car_type_name','worksheet_no'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('car_park_transfers.worksheet_no', 'like', '%' . $s . '%');
                // $q->orWhere('car_groups.name', 'like', '%' . $s . '%');
            }
        });
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
