<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use Kyslik\ColumnSortable\Sortable;

class ExpressWay extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, Sortable;
    protected $table = 'expressways';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
}
