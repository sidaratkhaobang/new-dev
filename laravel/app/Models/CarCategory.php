<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\Creator;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;

class CarCategory extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'car_categories';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'car_group_id'
    ];

    public $sortable = ['code', 'name'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where(function ($q2) use ($s,$request){
                $q2->where('car_categories.code', 'like', '%' . $s . '%');
                // $q2->orWhere('car_categories.name', 'like', '%' . $s . '%');
                $q2->orWhere('car_categories.reserve_small_size', $s);
                $q2->orWhere('car_categories.reserve_big_size', 'like', $s);
            });
            }
            // if (!empty($request->code)) {
            //     $q->orWhere('car_categories.id', $request->code);
            // }
            if (!empty($request->name)) {
                $q->Where('car_categories.id', $request->name);
            }
            if (!empty($request->car_group_id)) {
                $q->Where('car_groups.id', $request->car_group_id);
            }
        });
    }

    public function carGroup()
    {
        return $this->hasOne(CarGroup::class, 'id', 'car_group_id');
    }
}
