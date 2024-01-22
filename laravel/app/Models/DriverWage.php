<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class DriverWage extends Model
{
    use HasFactory, PrimaryUuid, Sortable, Creator;
    protected $table = 'driver_wages';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['name'];
    public $sortableAs = [
        'driver_wage_category_name',
        'service_type_name',
    ];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('driver_wages.name', 'like', '%' . $s . '%');
                $q->orWhere('driver_wage_categories.name', 'like', '%' . $s . '%');
            }
        });
    }

    public function driver_wage_category()
    {
        return $this->hasOne(DriverWageCategory::class, 'id', 'driver_wage_category_id');
    }

    public function service_type()
    {
        return $this->hasOne(ServiceType::class, 'id', 'service_type_id');
    }
}
