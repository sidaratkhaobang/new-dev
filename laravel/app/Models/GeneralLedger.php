<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;

class GeneralLedger extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    protected $table = 'general_ledgers';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'account',
        'description',
    ];
}
