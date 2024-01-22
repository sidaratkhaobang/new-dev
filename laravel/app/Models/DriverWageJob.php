<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;

class DriverWageJob extends Model
{
    use HasFactory, PrimaryUuid;

    protected $table = 'driver_wages_jobs';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function driver_wage()
    {
        return $this->hasOne(DriverWage::class, 'id', 'driver_wage_id');
    }
}
