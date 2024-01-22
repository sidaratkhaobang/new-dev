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

class CheckCredits extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;

    protected $table = 'check_credits';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = [
        'worksheet_no',
        'customer_type',
        'name',
        'brancheTable.name',
        'status',
    ];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($query) use ($request) {
            if (!empty($request->customer_type)) {
                $query->where('check_credits.customer_type', $request->customer_type);
            }
            if (!empty($request->customer_name)) {
                $query->where('check_credits.name', 'like', '%' . $request->customer_name . '%');
            }
            if (!empty($request->branch_id)) {
                $query->where('check_credits.branch_id', $request->branch_id);
            }
            if (!empty($request->status)) {
                $query->where('check_credits.status', $request->status);
            }
        });
    }

    public function brancheTable()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function createBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
