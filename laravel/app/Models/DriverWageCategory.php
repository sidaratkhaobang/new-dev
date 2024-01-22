<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class DriverWageCategory extends Model
{
    use HasFactory, PrimaryUuid, Sortable, Creator;
    protected $table = 'driver_wage_categories';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['name'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('driver_wage_categories.name', 'like', '%' . $s . '%');
            }
        });
    }
}
