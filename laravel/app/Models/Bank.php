<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Traits\Creator;
use App\Models\Traits\UpdateStatus;

class Bank extends Model
{
    use HasFactory, PrimaryUuid, Sortable, Creator, UpdateStatus, InteractsWithMedia;

    protected $table = 'banks';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
}
