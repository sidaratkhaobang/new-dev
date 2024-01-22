<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CheckDistanceLine extends Model
{
    use HasFactory,  PrimaryUuid, SoftDeletes, Creator, UpdateStatus;

    protected $table = 'check_distance_lines';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
}
