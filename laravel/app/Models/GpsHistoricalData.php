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

class GpsHistoricalData extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;
    protected $table = 'gps_historical_datas';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->worksheet_no)) {
                $q->where('id', $request->worksheet_no);
            }
            if (!empty($request->request_user)) {
                $q->where('created_by', $request->request_user);
            }
            if (!empty($request->request_date)) {
                $q->whereDate('created_at', $request->request_date);
            }
            if (!empty($request->status)) {
                $q->where('status', $request->status);
            }
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
