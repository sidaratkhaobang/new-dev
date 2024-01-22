<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerBillingAddress extends Model
{
    use HasFactory,  PrimaryUuid, Creator;

    protected $table = 'customer_billing_addresses';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'customer_id',
        'name',
        'tax_no',
        'address',
        'province_id',
        'email',
        'tel'
    ];

    public function province()
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }

    public function district()
    {
        return $this->hasOne(Amphure::class, 'id', 'district_id');
    }

    public function subdistrict()
    {
        return $this->hasOne(District::class, 'id', 'subdistrict_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
}
