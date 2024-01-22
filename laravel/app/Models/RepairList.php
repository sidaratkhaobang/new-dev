<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairList extends Model
{
    use HasFactory,  PrimaryUuid, SoftDeletes, Creator, UpdateStatus;

    protected $table = 'repair_lists';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];


    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->code)) {
                $q->where('id', $request->code);
            }
            if (!empty($request->name)) {
                $q->where('id', $request->name);
            }
            if (!empty($request->status)) {
                $q->where('status', $request->status);
            }
        });
    }
}
