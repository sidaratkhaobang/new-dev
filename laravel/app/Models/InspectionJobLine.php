<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class InspectionJobLine extends Model
{
    use HasFactory, PrimaryUuid, UpdateStatus, Sortable;

    protected $table = 'inspection_job_lines';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['name'];
}
