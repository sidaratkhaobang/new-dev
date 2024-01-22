<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarAccessory extends Model
{
    use HasFactory, PrimaryUuid, UpdateStatus;

    protected $table = 'car_accessories';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
    ];

    public function carAccessory()
    {
        return $this->hasOne(Accessories::class, 'id', 'accessory_id');
    }

}
