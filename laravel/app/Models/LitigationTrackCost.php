<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class LitigationTrackCost extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    protected $table = 'litigation_track_costs';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function bank()
    {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }
}