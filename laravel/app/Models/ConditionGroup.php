<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class ConditionGroup extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'condition_groups';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public $sortable = [
        'name',
    ];

    public function scopeSearch(Builder $query, $request): Builder
    {
        return $query->where(function ($query) use ($request) {
            if (!empty($request->category_name)) {
                $query->where('condition_groups.name', 'like', '%' . $request->category_name . '%');
            }
        });
    }

    public function condition_qoutations()
    {
        return $this->hasMany(ConditionQuotation::class, 'condition_group_id', 'id');
    }
}
