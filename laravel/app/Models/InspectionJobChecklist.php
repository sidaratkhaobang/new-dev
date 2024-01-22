<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class InspectionJobChecklist extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, UpdateStatus, Sortable, InteractsWithMedia;

    protected $table = 'inspection_job_checklists';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $hidden = array('media');
    public $timestamps = false;
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['name'];
}
