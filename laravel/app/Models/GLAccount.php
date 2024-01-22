<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;

class GLAccount extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, Sortable;
    protected $table = 'gl_accounts';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'account',
        'description',
        'type',
        'branch_id',
    ];

    public $sortableAs = ['name','account','description','type','branch_id','customer_group_name'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('gl_accounts.name', 'like', '%' . $s . '%');
                $q->orWhere('gl_accounts.description', 'like', '%' . $s . '%');
                $q->orWhere('customer_groups.name', 'like', '%' . $s . '%');
            }
        });
    }
}
