<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGroupRelation extends Model
{
    use HasFactory;
    protected $table = 'customers_groups_relation';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
