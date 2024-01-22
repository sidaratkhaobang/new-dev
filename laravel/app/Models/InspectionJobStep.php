<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;

class InspectionJobStep extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, UpdateStatus, Sortable, InteractsWithMedia;

    protected $table = 'inspection_job_steps';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['name'];
    protected $hidden = ['media'];

    public function InspectionForm()
    {
        return $this->hasOne(InspectionForm::class, 'id', 'inspection_form_id');
    }

    public function Department()
    {
        return $this->hasOne(Department::class, 'id', 'inspection_department_id');
    }

    public function UserInspector()
    {
        return $this->hasOne(User::class, 'id', 'inspector_id');
    }

    public function UserInspectorDriver()
    {
        return $this->hasOne(Driver::class, 'id', 'inspector_id');
    }
}
