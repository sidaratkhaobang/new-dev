<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class CarParkZone extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'car_park_zones';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['name', 'code'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('car_park_zones.code', 'like', '%' . $s . '%');
                $q->orWhere('car_park_zones.name', 'like', '%' . $s . '%');
            }
        });
    }

    public function scopeBranch($query): void
    {
        $query->where('car_park_zones.branch_id', get_branch_id());
    }
}
