<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\Creator;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;

class CarStatus extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'car_statuses';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['code', 'name'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('car_statuses.code', 'like', '%' . $s . '%');
                $q->orWhere('car_statuses.name', 'like', '%' . $s . '%');
            }
        });
    }
}
