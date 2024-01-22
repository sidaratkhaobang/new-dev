<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class DrivingSkillServiceType extends Model
{
    use HasFactory,Sortable;
    protected $table = 'driving_skills_service_types';
    // public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public $sortable = ['name'];
}
