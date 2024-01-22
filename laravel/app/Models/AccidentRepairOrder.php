<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use App\Models\Traits\Creator;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Kyslik\ColumnSortable\Sortable;

class AccidentRepairOrder extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, Sortable, InteractsWithMedia;

    protected $table = 'accident_repair_orders';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public $sortableAs = ['accident_worksheet','license_plate','case','cradle_name','worksheet_no','repair_date','amount_completed','actual_repair_date'];


    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->repair_worksheet_no)) {
                $q->where('accident_repair_orders.id', 'like', $request->repair_worksheet_no);
            }
            if (!empty($request->accident_worksheet_no)) {
                $q->where('accidents.id', 'like', $request->accident_worksheet_no);
            }
            if (!empty($request->license_plate)) {
                $q->where('cars.id', 'like', $request->license_plate);
            }
            if (!empty($request->status)) {
                $q->where('accident_repair_orders.status', 'like', $request->status);
            }
            if (!empty($request->accident_type)) {
                $q->where('accidents.accident_type', 'like', $request->accident_type);
            }
            // if (!empty($request->license_plate)) {
            //     $q->where('accidents.car_id', 'like', $request->license_plate);
            // }
            // if (!empty($request->status)) {
            //     $q->where('accidents.status', 'like', $request->status);
            // }
            
            
        });
    }
}
