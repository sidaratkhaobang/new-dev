<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, UpdateStatus, Sortable;

    protected $table = 'roles';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'description',
    ];

    public $sortable = ['name', 'description', 'updated_at'];
    public $sortableAs = ['department_name', 'section_name'];

    public function permission()
    {
        return $this->hasMany(RolePermission::class, 'role_id', 'id');
    }

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where(function ($q2) use ($s) {
                    $q2->where('roles.name', 'like', '%' . $s . '%');
                    $q2->orWhere('roles.description', 'like', '%' . $s . '%');
                });
            }
        });
    }
}
