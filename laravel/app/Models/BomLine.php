<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Kyslik\ColumnSortable\Sortable;

class BomLine extends Model
{
    use HasFactory, PrimaryUuid, Sortable ;
    protected $table = 'bom_lines';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
    ];

    public function carClass()
    {
        return $this->hasOne(CarClass::class, 'id', 'car_class_id');
    }

    public function color()
    {
        return $this->hasOne(CarColor::class, 'id', 'car_color_id');
    }
}
