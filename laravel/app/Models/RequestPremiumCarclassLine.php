<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class RequestPremiumCarclassLine extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    protected $table = 'request_premium_carclass_lines';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function getRequestPremiumPrice(){
        return $this->hasMany(RequestPremiumPrice::class,'request_premium_car_line_id');
    }

    public function getLongTermRentalLine(){
        return $this->hasOne(LongTermRentalLine::class,'id','lt_rental_line_id');
    }
}
