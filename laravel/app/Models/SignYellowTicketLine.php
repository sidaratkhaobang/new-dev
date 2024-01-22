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

class SignYellowTicketLine extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, UpdateStatus, Sortable, InteractsWithMedia;
    protected $table = 'sign_yellow_ticket_lines';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
    public $sortableAs = ['car.car_class','car.engine_no','car.chassis_no','hirePurchase.contract_no','car.license_plate'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('cars.engine_no', 'like', '%' . $s . '%');
            }
            if (!empty($request->actual_last_payment_date)) {
                $q->where('hire_purchases.actual_last_payment_date', 'like', $request->actual_last_payment_date);
            }
            if (!empty($request->contract_end_date)) {
                $q->where('hire_purchases.contract_no', 'like', $request->contract_no);
            }
            if (!empty($request->status)) {
                $q->where('ownership_transfers.status', 'like', $request->status);
            }
        });
    }
}
