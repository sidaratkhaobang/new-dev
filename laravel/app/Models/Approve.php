<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use App\Models\Traits\Creator;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approve extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus;

    protected $table = 'approves';
    public $incrementing = false;
    // public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
}
