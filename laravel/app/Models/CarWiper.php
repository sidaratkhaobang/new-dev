<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarWiper extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'car_wipers';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'version',
        'detail',
        'price',

    ];

    public $sortable = ['name', 'version', 'detail', 'price'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('car_wipers.name', 'like', '%' . $s . '%');
                $q->orWhere('car_wipers.version', 'like', '%' . $s . '%');
                $q->orWhere('car_wipers.detail', 'like', '%' . $s . '%');
                $q->orWhere('car_wipers.price', 'like', '%' . $s . '%');
            }
        });
    }
}
