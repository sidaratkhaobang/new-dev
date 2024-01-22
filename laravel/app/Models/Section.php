<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class Section extends Model
{
    use HasFactory, PrimaryUuid, Sortable, Creator;
    protected $table = 'sections';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'code',
    ];

    public $sortable = ['name'];
    public $sortableAs = ['department_name'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('sections.name', 'like', '%' . $s . '%');
            }
            if (!empty($request->department_id)) {
                $q->where('sections.department_id', $request->department_id);
            }
        });
    }
}
