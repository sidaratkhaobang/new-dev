<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;

class IEInspectionInstallEquipment extends Model
{
    use HasFactory;
    protected $table = 'ie_inspections_install_equipments';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}