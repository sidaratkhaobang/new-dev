<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class InspectionForm extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;

    protected $table = 'inspection_forms';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $hidden = array('media');
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['name'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('inspection_forms.name', 'like', '%' . $s . '%');
            }
        });
    }
}
