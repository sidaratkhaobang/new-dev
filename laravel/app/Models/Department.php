<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class Department extends Model
{
    use HasFactory, PrimaryUuid, Sortable, Creator;
    protected $table = 'departments';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'code',
    ];

    public $sortable = ['name'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('departments.name', 'like', '%' . $s . '%');
            }
        });
    }
}
