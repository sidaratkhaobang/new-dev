<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairOrderDate extends Model
{
    use HasFactory,  PrimaryUuid, SoftDeletes, Creator, UpdateStatus;

    protected $table = 'repair_order_date';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
}
