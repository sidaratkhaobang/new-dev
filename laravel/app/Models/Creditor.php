<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Creator;

class Creditor extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'creditors';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];
    public $sortable = ['code', 'name', 'tel', 'credit_terms'];
    public $sortableAs = ['province'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('creditors.code', 'like', '%' . $s . '%');
                $q->orWhere('creditors.name', 'like', '%' . $s . '%');
            }
        });
    }

    public function province()
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }
}
