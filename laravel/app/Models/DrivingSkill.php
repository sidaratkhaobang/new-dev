<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class DrivingSkill extends Model
{
    use HasFactory, PrimaryUuid, Sortable, Creator;
    protected $table = 'driving_skills';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public $sortable = ['name'];
    public $sortableAs = ['service_type_name'];
}
