<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LongTermRentalTorLineAccessory extends Model
{
    use HasFactory, PrimaryUuid;
    
    protected $table = 'lt_rental_tor_line_accessories';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
    ];

    public function accessory()
    {
        return $this->hasOne(Accessories::class, 'id', 'accessory_id');
    }
}
