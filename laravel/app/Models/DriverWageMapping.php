<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class DriverWageMapping extends Model
{
    use HasFactory;
    protected $table = 'driver_wages_mapping';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    // protected $fillable = [
    //     'id',
    //     'name',
    // ];

}
