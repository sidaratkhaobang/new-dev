<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;

class InstallEquipmentPOLine extends Model
{
    use HasFactory, PrimaryUuid;
    protected $table = 'install_equipment_po_lines';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function accessory()
    {
        return $this->hasOne(Accessories::class, 'id', 'accessory_id');
    }
}