<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class CarRentalCategory extends Model
{
    // use HasFactory;
    protected $table = 'cars_rental_categories';
    protected $keyType = 'string';
    public $timestamps = false;
    // protected $fillable = [
    //     'name',
    // ];

    // public $sortable = ['name'];

    // public function scopeSearch($query, $s)
    // {
    //     return $query->where(function ($q) use ($s) {
    //         if (!empty($s)) {
    //             $q->where('rental_categories.name', 'like', '%' . $s . '%');
    //             // $q->orWhere('service_types.name', 'like', '%' . $s . '%');
    //         }
    //     });
    // }
}
