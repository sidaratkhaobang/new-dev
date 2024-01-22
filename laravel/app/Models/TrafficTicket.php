<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrafficTicket extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    protected $table = 'traffic_tickets';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
    public $sortable = ['created_at'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('traffic_tickets.traffic_ticket_no', 'like', '%' . $s . '%');
            }
            if (!empty($request->traffic_ticket_no)) {
                $q->where('traffic_tickets.id', 'like', $request->traffic_ticket_no);
            }
            if (!empty($request->car_id)) {
                $q->where('traffic_tickets.car_id', $request->car_id);
            }
            if (!empty($request->document_type)) {
                $q->where('traffic_tickets.document_type', $request->document_type);
            }
            if (!empty($request->police_station_id)) {
                $q->where('traffic_tickets.police_station_id', $request->police_station_id);
            }
            if (!empty($request->offense_date)) {
                $q->where('traffic_tickets.offense_date', $request->offense_date);
            }
            if (!empty($request->status)) {
                $q->where('traffic_tickets.status', $request->status);
            }
        });
    }
}
