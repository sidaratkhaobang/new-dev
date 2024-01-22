<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;

class InstallEquipmentInspection extends Model
{
    use HasFactory, PrimaryUuid;
    protected $table = 'install_equipment_inspections';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

}