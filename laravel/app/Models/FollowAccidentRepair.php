<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UpdateStatus;
use App\Models\Traits\Creator;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;


class FollowAccidentRepair extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator;

    protected $table = 'follow_accident_repairs';
    public $incrementing = false;
    // public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    // public $sortableAs = ['follow_up_status'];
}
