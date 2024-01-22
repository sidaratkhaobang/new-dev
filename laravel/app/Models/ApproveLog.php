<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UpdateStatus;
use App\Models\Traits\Creator;

class ApproveLog extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus;

    protected $table = 'approve_logs';
    public $incrementing = false;
    // public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
}
