<?php

namespace App\Models;

use App\Enums\ConsentType;
use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory,  PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'customers';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'customer_code',
        'debtor_code',
        'customer_type',
        'customer_grade',
        /* 'email', */
        'prefixname_th',
        'fullname_th',
        'prefixname_en',
        'fullname_en',
        'address',
        'province_id',
        'fax',
        'tel',
        'phone',
        'sale_id',
    ];

    protected $hidden = ['consents'];

    public $sortable = ['customer_code', 'name', 'debtor_code', 'customer_type', 'created_at'];
    public $sortableAs = ['province', 'sale_name'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('customers.customer_code', 'like', '%' . $s . '%');
                $q->orWhere('customers.debtor_code', 'like', '%' . $s . '%');
                $q->orWhere('customers.name', 'like', '%' . $s . '%');
                $q->orWhere('customers.fullname_th', 'like', '%' . $s . '%');
                $q->orWhere('customers.fullname_en', 'like', '%' . $s . '%');
                $q->orWhere('users.name', 'like', '%' . $s . '%');
                $q->orWhere('provinces.name_th', 'like', '%' . $s . '%');
            }
        });
    }

    public function getCustomerGroupArray(){
        return CustomerGroupRelation::join('customer_groups', 'customer_groups.id', '=', 'customers_groups_relation.customer_group_id')
            ->select('customer_groups.id as id', 'customer_groups.name as name')
            ->where('customers_groups_relation.customer_id', $this->id)
            ->pluck('customer_groups.id')
            ->toArray();
    }

    public function province()
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }

    public function consents()
    {
        return $this->hasMany(CustomerConsent::class, 'customer_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function customer_group_relations(){
        return $this->hasMany(CustomerGroupRelation::class, 'customer_id', 'id');
    }
}
