<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory,  PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'promotions';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public $sortable = ['name', 'sku', 'priority', 'start_date', 'end_date', 'status'];
    public $sortableAs = ['branch_name'];

    protected $hidden = ['freeCarClasses', 'freeProductAdditionals', 'incompatibles'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('promotions.name', 'like', '%' . $s . '%');
                $q->orWhere('promotions.code', 'like', '%' . $s . '%');
            }
            if (!empty($request->name)) {
                $q->where('promotions.id', $request->name);
            }
            if (!empty($request->branch_id)) {
                $q->where('promotions.branch_id', $request->branch_id);
            }
            if (!empty($request->type_id)) {
                $q->where('promotions.promotion_type', $request->type_id);
            }
            if (!empty($request->start_date) && !empty($request->end_date)) {
                $q->whereDate('promotions.start_date', $request->start_date);
                $q->whereDate('promotions.end_date', $request->end_date);
            }
            if (!empty($request->start_date)) {
                $q->whereDate('promotions.start_date', '<=', $request->start_date);
            }
            if (!empty($request->end_date)) {
                $q->whereDate('promotions.end_date', '>=', $request->end_date);
            }
        });
    }

    public function freeCarClasses()
    {
        return $this->hasMany(PromotionFreeCarClass::class, 'promotion_id', 'id');
    }

    public function freeProductAdditionals()
    {
        return $this->hasMany(PromotionFreeProductAdditional::class, 'promotion_id', 'id');
    }

    public function incompatibles()
    {
        return $this->hasMany(PromotionIncompatible::class, 'promotion_id', 'id');
    }

    function getFreeCarClassName()
    {
        $return = null;
        $free = $this->freeCarClasses;
        if (sizeof($free) > 0) {
            foreach ($free as $item) {
                $carClass = $item->carClass;
                if ($carClass) {
                    $return .= $carClass->name;
                }
            }
        }
        return $return;
    }
}
