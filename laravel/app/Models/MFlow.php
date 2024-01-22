<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MFlow extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;

    protected $table = 'm_flows';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('m_flows.worksheet_no', 'like', '%' . $s . '%');
            }
            if (!empty($request->m_flow_id)) {
                $q->where('m_flows.id', $request->m_flow_id);
            }
            if (!empty($request->car_id)) {
                $q->where('m_flows.car_id', $request->car_id);
            }
            if (!empty($request->expressway_id)) {
                $q->where('m_flows.expressway_id', $request->expressway_id);
            }
            if (!empty($request->offense_date)) {
                $q->whereDate('m_flows.offense_date', $request->offense_date);
            }
            if (!empty($request->status)) {
                $q->where('m_flows.status', $request->status);
            }
        });
    }

    public function expressWay()
    {
        return $this->hasOne(ExpressWay::class, 'id', 'expressway_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
