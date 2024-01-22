<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GLAccountCustomerGroup extends Model
{
    use HasFactory;
    protected $table = 'gl_accounts_customer_groups';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
