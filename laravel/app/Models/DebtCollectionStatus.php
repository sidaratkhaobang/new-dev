<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\Creator;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebtCollectionStatus extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus;
    protected $table = 'debt_collection_status';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
}
