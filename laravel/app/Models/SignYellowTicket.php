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

class SignYellowTicket extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;
    protected $table = 'sign_yellow_tickets';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
    // public $sortableAs = ['car.car_class','car.engine_no','car.chassis_no','hirePurchase.contract_no','car.license_plate'];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            // if (!empty($s)) {
            //     $q->where('cars.engine_no', 'like', '%' . $s . '%');
            // }
            if (!empty($request->car_id)) {
                $q->where('sign_yellow_tickets.car_id', 'like', $request->car_id);
            }
            if (!empty($request->car_class)) {
                $q->where('car_classes.id', 'like', $request->car_class);
            }
            if (!empty($request->created_at)) {
                $q->whereDate('sign_yellow_tickets.created_at', 'like', $request->created_at);
            }
            if (!empty($request->responsible)) {
                $q->where('sign_yellow_ticket_lines.institution', 'like', $request->responsible);
            }
            if (!empty($request->status)) {
                $q->where('sign_yellow_tickets.status', $request->status);
            }
        });
    }
    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }
}
