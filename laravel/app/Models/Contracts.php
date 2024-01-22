<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Contracts extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;

    protected $table = 'contracts';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'worksheet_no',
    ];

    public $sortable = [
        'worksheet_no',
        'created_at'
    ];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($query) use ($request) {
            if (!empty($request->worksheet_no)) {
                $query->where('contracts.id', $request->worksheet_no);
            }
            if (!empty($request->contract_type)) {
                $query->where('contracts.job_type', $request->contract_type);
            }
            if (!empty($request->status)) {
                $query->where('contracts.status', $request->status);
            }
            if (!empty($request->customer_id)) {
                $query->where('contracts.customer_id', $request->customer_id);
            }
        });
    }

    public function job()
    {
        return $this->morphTo();
    }

    public function contractline()
    {
        return $this->hasMany(ContractLines::class, 'contract_id');
    }

    public function contract_forms()
    {
        return $this->hasMany(ContractForm::class, 'contract_id', 'id');
    }

    public function contract_log()
    {
        return $this->hasMany(ContractLogs::class, 'contract_id', 'id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
