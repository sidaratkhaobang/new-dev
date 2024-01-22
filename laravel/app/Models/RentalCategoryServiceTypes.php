<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class RentalCategoryServiceTypes extends Model
{
    use HasFactory;
    protected $table = 'rental_categories_service_types';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
