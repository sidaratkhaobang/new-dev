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
class AccidentRepairOrderLine extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'accident_repair_order_lines';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    // protected $fillable = [
    //     'id',
    // ];
}
