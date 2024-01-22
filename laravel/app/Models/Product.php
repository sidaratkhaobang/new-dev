<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'products';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['name', 'sku', 'standard_price'];
    public $sortableAs = ['branch_name'];


    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('products.name', 'like', '%' . $s . '%');
                $q->orWhere('products.sku', 'like', '%' . $s . '%');
            }
        });
    }
}
