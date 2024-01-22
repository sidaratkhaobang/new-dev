<?php

namespace App\Models;

use App\Models\LongTermRentalLine;
use App\Models\RequestPremiumCarclassLine;
use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
class RequestPremium extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    protected $table = 'request_premiums';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
    public $sortable = ['job_id', 'job_type', 'rental_month', 'customer_name', 'customer_email','status'];

    public function scopegetRequestPremiumList($query)
    {
        $RequestPremiumList = $query->select('id','job_id as value')->get();

        if(!empty($RequestPremiumList)){
            return $RequestPremiumList;
        }else{
            return [];
        }
    }
    public function getRequestCarLine()
    {
        return $this->hasMany(RequestPremiumCarclassLine::class,'request_premium_id');
    }

    public function getLongTermRentalLine(){
        return $this->hasMany(LongTermRentalLine::class,'lt_rental_id','job_id');

    }

    public function getLongTermRental(){
        return $this->hasOne(LongTermRental::class,'id','job_id');

    }

    public function getCustomer(){
        return $this->hasOne(Customer::class,'customer_id','id');
    }



}
