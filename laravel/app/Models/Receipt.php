<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'receipts';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function reference()
    {
        return $this->morphTo();
    }

    public function parent()
    {
        return $this->hasOne(Receipt::class, 'id', 'parent_id');
    }
}
