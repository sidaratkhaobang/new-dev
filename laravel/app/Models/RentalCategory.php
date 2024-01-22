<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class RentalCategory extends Model
{
    use HasFactory, PrimaryUuid, Sortable, Creator;
    protected $table = 'rental_categories';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['name'];
    public $sortableAs = ['service_type_name'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('rental_categories.name', 'like', '%' . $s . '%');
                // $q->orWhere('service_types.name', 'like', '%' . $s . '%');
            }
        });
    }
}
