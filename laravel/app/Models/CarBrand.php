<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CarBrand extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, InteractsWithMedia;
    use Sortable;
    
    protected $table = 'car_brands';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];
    public $sortable = ['code', 'name', 'created_at', 'updated_at'];
    public $sortableAs = ['creator_name', 'updater_name'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('car_brands.code', 'like', '%' . $s . '%');
                $q->orWhere('car_brands.name', 'like', '%' . $s . '%');
            }
        });
    }

    public function createBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updateBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
