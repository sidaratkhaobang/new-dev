<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class InspectionFormChecklist extends Model
{
    use HasFactory, PrimaryUuid, UpdateStatus, Sortable;
    protected $table = 'inspection_form_checklists';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    // protected $fillable = [
    //     'id',
    //     'name',
    // ];

    public $sortable = ['name'];
}
