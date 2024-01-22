<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;

class DriverWageRelation extends Model
{
    use HasFactory, PrimaryUuid;

    protected $table = 'driver_wages_relation';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'driver_id',
        'driver_wage_id',
        'amount',
        'amount_type',
    ];

    public function driver_wage()
    {
        return $this->hasOne(DriverWage::class, 'id', 'driver_wage_id');
    }
}
