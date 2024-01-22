<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ImportCarLine extends Model
{
    use HasFactory, PrimaryUuid, Sortable;

    protected $table = 'import_car_lines';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id'
    ];
    public $sortableAs = ['engine_no', 'chassis_no', 'delivery_date', 'delivery_location', 'creditor_name', 'po_no'];

    public function scopeBranch($query)
    {
        $user = Auth::user();
        return $query->where(function ($q) use ($user) {

            if ($user->branch && $user->branch->is_main == STATUS_ACTIVE) {
                // do nothing, let the query continue
            } else {
                $q->whereRaw(STATUS_DEFAULT); // return an empty result set if not main branch
            }
        });
    }

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where(function ($q2) use ($s, $request) {
                    $q2->where('purchase_orders.po_no', 'like', '%' . $s . '%');
                });
            }
            if (!empty($request->po_no)) {
                $q->where('purchase_orders.id', 'like', $request->po_no);
            }
            if (!empty($request->engine_no)) {
                $q->where('import_car_lines.id', 'like', '%' . $request->engine_no . '%');
            }
            if (!empty($request->chassis_no)) {
                $q->where('import_car_lines.id', 'like', '%' . $request->chassis_no . '%');
            }
            if (!empty($request->delivery_location)) {
                $q->where('import_car_lines.delivery_location', $request->delivery_location);
            }
            if (!empty($request->from_delivery_date) || !empty($request->to_delivery_date)) {
                $q->whereBetween('import_car_lines.delivery_date', [$request->from_delivery_date . " 00:00:00", $request->to_delivery_date . " 23:59:59"]);
            }
            if (!empty($request->dealer)) {
                $q->where('creditors.id', $request->dealer);
            }
            if (!empty($request->status)) {
                $q->where('import_car_lines.status_delivery', $request->status);
            }
        });
    }

    public function drivingJob()
    {
        return $this->morphMany(DrivingJob::class, 'job');
    }

    public function importCar()
    {
        return $this->hasOne(ImportCar::class, 'id', 'import_car_id');
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'id');
    }

    public function insuranceLot()
    {
        return $this->hasOne(InsuranceLot::class, 'id', 'lot_id');
    }
}
