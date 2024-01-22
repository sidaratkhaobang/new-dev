<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class InspectionFormSection extends Model
{
    use HasFactory, PrimaryUuid, UpdateStatus, Sortable;

    protected $table = 'inspection_form_sections';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    // protected $fillable = [
    //     'id',
    //     'name',
    // ];

    public $sortable = ['name'];
}
