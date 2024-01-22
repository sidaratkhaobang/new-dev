<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class CancelInsurance extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    protected $table = 'cancel_vmi_cmis';
    public $incrementing = false;
    protected $keyType = 'string';

    public function ref()
    {
        return $this->morphTo();
    }

    public function lot()
    {
        return $this->belongsTo(InsuranceLot::class, 'lot_id');
    }
}