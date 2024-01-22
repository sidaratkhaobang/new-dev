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


class Litigation extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;
    protected $table = 'litigations';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('litigations.worksheet_no', 'like', '%' . $s . '%');
            }
            if (!empty($request->worksheet_no)) {
                $q->where('litigations.id', 'like', $request->worksheet_no);
            }
            if (!empty($request->accuser_defendant)) {
                $q->where('litigations.accuser_defendant', 'like',  '%' . $request->accuser_defendant . '%');
            }
            if (!empty($request->tls_type)) {
                $q->where('litigations.tls_type', $request->tls_type);
            }
            if (!empty($request->case_type)) {
                $q->where('litigations.case_type', $request->case_type);
            }
            if (!empty($request->status)) {
                $q->where('litigations.status', $request->status);
            }
            if (!empty($request->due_date)) {
                $q->where('litigations.due_date', $request->due_date);
            }
        });
    }
}